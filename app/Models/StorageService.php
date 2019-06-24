<?php

namespace App\Models;


use App\Exceptions\StorageException;
use App\Http\Requests\ContentInfo;
use App\Http\Requests\ContentList;
use App\Http\Requests\ContentEdit;
use App\Http\Requests\ContentSave;
use App\Http\Requests\ContentPaste;
use App\Http\Requests\ContentRemove;
use App\Http\Requests\ContentRename;
use App\Http\Requests\DirectoryMake;
use App\Http\Requests\FileDownload;
use App\Http\Requests\FileUpload;
use App\Http\Requests\NewFile;
use App\Http\Requests\LockRequest;
use App\Http\Requests\ChangeFileState;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Session;


class StorageService
{

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * StorageService constructor.
	 *
	 * @param FilesystemAdapter $storage
	 */
	public function __construct(FilesystemAdapter $storage)
	{

		$this->storage = $storage;

	}

	public function login($path, $username, $password) {
		$content = $this->storage->get($path);
		$userInfo = $username.', '.$password;
		if (strpos($content, $userInfo) == FALSE)
			return false;
		else {
			$pos = strpos($content, $userInfo);
			$userData = explode("<br>", substr($content, $pos))[0];
			$role = str_replace($userInfo.',', "", $userData);
			$role = trim($role);
			return $role;
		}
	}

	public function lockDelete(LockRequest $request) {
		$path = $request->path;
		$lockDeleteInfoPath = env('DELETE_LOCK_PATH', '/');
		if ($this->isFile($lockDeleteInfoPath)) {
			$this->storage->append($lockDeleteInfoPath, $path);
		}

	}
	public function lockEdit(LockRequest $request) {
		$path = $request->path;
		$lockEditInfoPath = env('EDIT_LOCK_PATH', '/');
		if ($this->isFile($lockEditInfoPath)) {
			$this->storage->append($lockEditInfoPath, $path);
		}

	}

	/** Get directory content
	 *
	 * @param ContentList $request
	 *
	 * @return array
	 */
	public function list(ContentList $request)
	{

		$path = $request->path;
		$this->checkLocked($path);
		$dirInfo = $this->pathInfo($path);
		$dirList = $this->directories($path);
		$filesList = $this->files($path);
		$qNavigation = $this->quickNavigation($path);

		return [
			'info' => [
				'dirPath' => $path,
				'dirName' => $dirInfo['basename'] === '.' ? 'Root' : $dirInfo['basename'],
				'dirCount' => count($dirList),
				'filesCount' => count($filesList)
			],
			'directories' => $dirList,
			'files' => $filesList,
			'quick_navigation' => $qNavigation
		];

	}

	/**
	 * Get file info
	 *
	 * @param ContentInfo $request
	 *
	 * @return array
	 *
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function info(ContentInfo $request)
	{

		$path = $request->path;
		
		$this->checkExists($path);

		$size = $this->isFile($path) ? $this->fileSize($path) : $this->directorySize($path);
		$lastModified = $this->storage->lastModified($path);

		return  [
			'size' => $size,
			'modified' => $lastModified !== false ? date('d-m-Y H:i:s', $this->storage->lastModified($path)) : '---'
		];

	}

	/**
	 * Edit file
	 *
	 * @param ContentEidt $request
	 *
	 * @return array
	 *
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function edit(ContentEdit $request)
	{

	   	$path = $request->path;
	   	$this->checkExists($path);
	   	$this->checkLockedEdit($path);
	   	$content = $this->storage->get($path);
	   	return $content;

	}

	public function save(ContentSave $request)
	{
	   	$path = $request->path;
	   	$content = $request->content;
	   	$this->checkExists($path);
	   	$user = session()->get('username', '');
	   	$this->backup($user, $path, 'edit');
	   	// $this->backup($path);
	   	$success = $this->storage->put($path, $content);
	   	return $success;

	}

	public function makePublic(ChangeFileState $request)
	{
		$user = session()->get('username', '');
		$path = $request->path;
		$this->checkExists($path);
		if ($this->isFile($path)) {
			$success = $this->storage->setVisibility($path, 'public');
			$this->backup($user, $path, 'makePublic');
			return $success;
		}
	}

	public function makePrivate(ChangeFileState $request)
	{
		$user = session()->get('username', '');
		$path = $request->path;
		$this->checkExists($path);
		if ($this->isFile($path)) {
			$success = $this->storage->setVisibility($path, 'private');
			$this->backup($user, $path, 'makePrivate');
			return $success;
		}
	}

	/**
	 * Download file
	 *
	 * @param FileDownload $request
	 *
	 * @return StreamedResponse
	 * @throws StorageException
	 */
	public function download(FileDownload $request)
	{

		$path = $request->path;

		$this->checkExists($path);
		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'download');
	   	$this->backup($user, $path, 'download');

		return $this->storage->download($path);

	}

	/**
	 * Upload files
	 *
	 * @param FileUpload $request
	 *
	 * @return void
	 * @throws StorageException
	 */
	public function upload(FileUpload $request)
	{
		$user = session()->get('username', '');

		$filesList = $request->files_list;

		$path = $request->path;

		if (count($filesList) === 0) {
			throw new StorageException('Files list is empty.');
		}
		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'upload');
		foreach ($filesList as $file) {

			$this->uploadFile($file, $path);

		}

	}

	/**
	 * New file
	 *
	 * @param NewFile $request
	 *
	 * @return void
	 * @throws StorageException
	 */
	public function new(NewFile $request)
	{

		$user = session()->get('username', '');

		$file = $request->file;
		$name = $request->name;
		$path = $request->path;

		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'new');

	   	$sourcePath = $path . '/' . $file->getClientOriginalName();

		$uploadFileName = $this->getCopyFilePath($sourcePath, $path, $name);

		$this->storage->putFileAs('.', $file, $uploadFileName);

		$this->backup($user, $uploadFileName, 'createFile');

	}

	/**
	 * Create directory
	 *
	 * @param DirectoryMake $request
	 *
	 * @return void
	 * @throws StorageException
	 */
	public function makeDirectory(DirectoryMake $request)
	{
		$user = session()->get('username', '');

		$parentDirectory = $request->path;
		$directoryName = $request->name;

		$sourcePath = $parentDirectory . '/' . $directoryName;

		$this->checkUnique($sourcePath);

		$this->createDirectory($sourcePath);
		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'make directory');
	   	$this->backup($user, $sourcePath, 'makeDirectory');
	}

	/**
	 * Rename file or directory
	 *
	 * @param ContentRename $request
	 *
	 * @return void
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function rename(ContentRename $request)
	{

		$sourcePath = $request->path;
		$newName = $request->name;

		$info = pathinfo($sourcePath);

		$destinationPath = $info['dirname'] . '/' . $newName;
		$this->checkLocked($path);
		$this->checkUnique($destinationPath);

		$user = session()->get('username', '');
	   	

	   	// $this->backup($sourcePath);
		$this->backup($user, $path, 'rename');

		if ($this->isFile($sourcePath)) {

			$this->storage->move($sourcePath, $destinationPath);

		} elseif ($this->isDirectory($sourcePath)) {

			$this->createDirectory($destinationPath);

			$subDirectories = $this->directories($sourcePath);

			foreach ($subDirectories as $subDirectory) {
				$this->moveDirectory($subDirectory['path'], $destinationPath);
			}

			$files = $this->files($sourcePath);

			foreach ($files as $file) {
				$this->moveFile($file['path'], $destinationPath);
			}

			$this->removeDirectory($sourcePath);

		}

	}

	/**
	 * Paste content
	 *
	 * @param ContentPaste $request
	 *
	 * @return void
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function paste(ContentPaste $request)
	{
		$user = session()->get('username', '');

		$sourcePathList = $request->source_path_list;
		$destinationPath = $request->destination_path;
		$operation = $request->operation;

		if ($operation !== 'copy' && $operation !== 'cut') {
			throw new StorageException('Unknown operation type.');
		}

		foreach ($sourcePathList as $sourcePath) {

			if ($operation === 'copy') {

				$this->backup($user, $sourcePath, 'copy');
				$this->copy($sourcePath, $destinationPath);

			} else {
				$this->backup($user, $sourcePath, 'cut');
				$this->move($sourcePath, $destinationPath);

			}
		}
		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'paste');

	}

	/**
	 * Remove content
	 *
	 * @param ContentRemove $request
	 *
	 * @return void
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function remove(ContentRemove $request)
	{
		$user = session()->get('username', '');
	   	// $this->log($user, $path, 'remove');

		$pathList = $request->path_list;

		foreach ($pathList as $path) {
			$this->checkLockedDelete($path);
			$this->backup($user, $path, 'remove');
			if ($this->isFile($path)) {

				$this->removeFile($path);

			} elseif ($this->isDirectory($path)) {

				$this->removeDirectory($path);

			} else {

				throw new StorageException('Can not remove files or directories');

			}

		}
		

	}



	/**
	 * Get file size
	 *
	 * @param string $path
	 *
	 * @return integer|null
	 */
	private function fileSize($path)
	{

		return (int) $this->storage->size($path);

	}

	/**
	 * Get directory size
	 *
	 * @param string $path
	 *
	 * @return integer
	 */
	private function directorySize($path)
	{

		$size = 0;

		$files = $this->filesAll($path);

		foreach ($files as $file) {

			$size += $this->storage->size($file['path']);

		}

		return $size;

	}

	/**
	 * Get path info
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	private function pathInfo($path)
	{

		$pathInfo = pathinfo($path);

		return [
			'path' => $path,
			'basename' => $pathInfo['basename'],
			'dirname' => $pathInfo['dirname'],
			'filename' => $pathInfo['filename'],
			'extension' => $pathInfo['extension'] ?? ''
		];

	}

	/**
	 * Get directories list
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	private function directories($directory)
	{

		$s3Directories = $this->storage->directories($directory);

		$default_lock_path = ' User_manager/ Logging_folder/ Locked_data/';

		$directoriesList = [];
		$role = session()->get('role', '');

		foreach ($s3Directories as $s3Directory) {

			$dirInfo = pathinfo($s3Directory);

			$directoriesList[] = [
				'name' => $dirInfo['basename'],
				'path' => $s3Directory,
				'locked' => $role != 'admin' && strpos($default_lock_path, $s3Directory) != FALSE ? 'lock' : 'unlock',
			];

		}

		return $directoriesList;

	}

	/**
	 * Get directories list with all subdirectories
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	private function directoriesAll($directory)
	{

		$s3Directories = $this->storage->allDirectories($directory);

		$directoriesList = [];

		foreach ($s3Directories as $s3Directory) {

			$dirInfo = pathinfo($s3Directory);

			$directoriesList[] = [
				'name' => $dirInfo['basename'],
				'path' => $s3Directory,
			];

		}

		return $directoriesList;

	}

	/**
	 * Get Lock status
	 *
	 * @param string $path
	 *
	 * @return status
	 */
	private function getLockStatus($path) {
		$lockedEditFileInfoPath = env('EDIT_LOCK_PATH');
		$lockedEditFiles = $this->storage->get($lockedEditFileInfoPath);
		$lockedEditFiles = ' '. $lockedEditFiles;

		$lockedDeleteFileInfoPath = env('DELETE_LOCK_PATH');
		$lockedDeleteFiles = $this->storage->get($lockedDeleteFileInfoPath);
		$lockedDeleteFiles = ' '. $lockedDeleteFiles;

		if (strpos($lockedEditFiles, $path)) return 2;
		else if (strpos($lockedDeleteFiles, $path)) return 1;
		else return 0;
	}

	/**
	 * Get files list for directory
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	private function files($directory)
	{

		$s3Files = $this->storage->files($directory);

		$filesList = [];

		foreach ($s3Files as $s3File) {

			$fileInfo = pathinfo($s3File);

			$filesList[] = [
				'name' => $fileInfo['basename'],
				'path' => $s3File,
				'public_status' => $this->storage->getVisibility($s3File),
				'lock_status' => $this->getLockStatus($s3File)
			];

		}

		return $filesList;

	}

	/**
	 * Get files list for directory and all subdirectories
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	private function filesAll($directory)
	{

		$s3Files = $this->storage->allFiles($directory);

		$filesList = [];

		foreach ($s3Files as $s3File) {

			$fileInfo = pathinfo($s3File);

			$filesList[] = [
				'name' => $fileInfo['basename'],
				'path' => $s3File,
				'public_status' => $this->storage->getVisibility($s3File),
				'lock_status' => $this->getLockStatus($s3File)
			];

		}

		return $filesList;

	}

	/**
	 * Get list of path parts for navigation
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	private function quickNavigation($directory)
	{

		$directoriesList = [];

		if ($directory === '.') {

			return $directoriesList;

		}

		$directoriesList = [
			[
				'name' => 'Root',
				'path' => '.'
			]
		];

		$pathParts = explode('/', $directory);

		if (count($pathParts) === 1) {

			return $directoriesList;

		}

		$path = '/';

		foreach ($pathParts as $s3Directory) {

			if ($s3Directory === '') {
				continue;
			}

			$path .= $s3Directory;

			$directoriesList[] = [
				'name' => $s3Directory,
				'path' => $path,
			];

			$path .= '/';

		}

		return array_slice($directoriesList, 0, -1);

	}

	/**
	 * Check if file/directory name is unique
	 *
	 * @param string $path
	 *
	 * return void
	 * @throws StorageException
	 */
	private function checkUnique($path)
	{

		if ($this->storage->exists($path)) {
			throw new StorageException('File or directory already exists.');
		}

	}

	/**
	 * Check if file/directory exists
	 *
	 * @param string $path
	 *
	 * return void
	 * @throws StorageException
	 */
	private function checkExists($path)
	{

		if (!$this->storage->exists($path)) {
			throw new StorageException('No such file or directory.');
		}

	}

	/**
	 * Check if file/directory is locked
	 *
	 * @param string $path
	 *
	 * return void
	 * @throws StorageException
	 */
	private function checkLocked($path)
	{
		$default_list = ' Logging_folder/ User_manager/ Locked_data/';
		$role = session()->get('role', '');
		if ($role != 'admin') {
			if(strpos($default_list, $path) != FALSE)
				throw new StorageException("You don't have permission to this path");
		}
	}

	/**
	 * Check if file/directory is locked
	 *
	 * @param string $path
	 *
	 * return void
	 * @throws StorageException
	 */
	private function checkLockedDelete($path)
	{
		$role = session()->get('role', '');

		$this->checkLocked($path);

		$lockedEditFileInfoPath = env('EDIT_LOCK_PATH');
		$lockedEditFiles = $this->storage->get($lockedEditFileInfoPath);
		$lockedEditFiles = ' '.$lockedEditFiles;

		$lockedDeleteFileInfoPath = env('DELETE_LOCK_PATH');
		$lockedDeleteFiles = $this->storage->get($lockedDeleteFileInfoPath);
		$lockedDeleteFiles = ' '.$lockedDeleteFiles;

		if ($role == 'user') {
			if(strpos($lockedEditFiles, $path) != FALSE || strpos($lockedDeleteFiles, $path) != FALSE)
				throw new StorageException("You don't have permission to this path");
		}
	}

	/**
	 * Check if file/directory is locked
	 *
	 * @param string $path
	 *
	 * return void
	 * @throws StorageException
	 */
	private function checkLockedEdit($path)
	{
		$role = session()->get('role', '');

		$lockedEditFileInfoPath = env('EDIT_LOCK_PATH');
		$lockedEditFiles = $this->storage->get($lockedEditFileInfoPath);
		$lockedEditFiles = ' '.$lockedEditFiles;

		if ($role == 'user') {
			if(strpos($lockedEditFiles, $path) != FALSE)
				throw new StorageException("You don't have permission to this path");
		}
	}

	

	/**
	 * Remove directory
	 *
	 * @param string $path
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function removeDirectory($path)
	{

		$this->checkExists($path);

		return $this->storage->deleteDirectory($path);

	}

	/**
	 * Remove file
	 *
	 * @param string $path
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function removeFile($path)
	{

		$this->checkExists($path);

		return $this->storage->delete($path);

	}

	/**
	 * Copy file
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function copyFile($sourcePath, $destinationPath)
	{

		$this->checkExists($sourcePath);

		try {

			$filePath = $this->getCopyFilePath($sourcePath, $destinationPath);

			return $this->storage->copy($sourcePath, $filePath);

		} catch (\Exception $e) {

			throw new StorageException('Can not copy files.' . $e);

		}

	}

	/**
	 * Move file
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function moveFile($sourcePath, $destinationPath)
	{

		$this->checkExists($sourcePath);

		try {

			$filePath = $this->getCopyFilePath($sourcePath, $destinationPath);

			return $this->storage->move($sourcePath, $filePath);

		} catch (\Exception $e) {

			throw new StorageException('Can not move files.');

		}

	}



	/**
	 * Create directory
	 *
	 * @param string $path
	 *
	 * @return boolean
	 */
	private function createDirectory($path)
	{

		return $this->storage->makeDirectory($path);

	}

	/**
	 * Copy directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function copyDirectory($sourcePath, $destinationPath)
	{

		$this->checkExists($sourcePath);

		try {

			$directoryPath = $this->getCopyDiretoryPath($sourcePath, $destinationPath);

			$this->createDirectory($directoryPath);

			$subDirectories = $this->directories($sourcePath);

			foreach ($subDirectories as $subDirectory) {

				$this->copyDirectory($subDirectory['path'], $directoryPath);

			}

			$filesList = $this->files($sourcePath);

			foreach ($filesList as $file) {

				$this->copyFile($file['path'], $directoryPath);

			}

		} catch (\Exception $e) {

			return false;

		}

		return true;

	}

	/**
	 * Move directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return boolean
	 * @throws StorageException
	 */
	private function moveDirectory($sourcePath, $destinationPath)
	{

		try {

			$this->copyDirectory($sourcePath, $destinationPath);

			$this->removeDirectory($sourcePath);

		} catch (\Exception $e) {

			return false;

		}

		return true;

	}

	/**
	 * Get file name for copying it to destination directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return string
	 */
	private function getCopyFilePath($sourcePath, $destinationPath, $filename = null)
	{

		$pathInfo = $this->pathInfo($sourcePath);
		$newFilename = $filename == null ? $pathInfo['basename'] : $filename;

		$newFilePath = $destinationPath . '/' . $newFilename;

		$index = 1;

		do {

			try {

				$this->checkExists($newFilePath);

				$fileExist = true;

				$newFilePath = $destinationPath . '/' . $pathInfo['filename'] . '_copy(' . $index . ').' . $pathInfo['extension'];

				$index++;



			} catch (StorageException $e) {

				$fileExist = false;

			}


		} while ($fileExist === true);


		return $newFilePath;

	}

	/**
	 * Get directory name for copying it to destination directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return string
	 */
	private function getCopyDiretoryPath($sourcePath, $destinationPath)
	{

		$pathInfo = $this->pathInfo($sourcePath);

		$newFilePath = $destinationPath . '/' . $pathInfo['basename'];

		$index = 1;

		do {

			try {

				$this->checkExists($newFilePath);

				$fileExist = true;

				$newFilePath = $destinationPath . '/' . $pathInfo['filename'] . '_copy(' . $index . ')';

				$index++;



			} catch (StorageException $e) {

				$fileExist = false;

			}


		} while ($fileExist === true);


		return $newFilePath;

	}

	/**
	 * Upload file
	 *
	 * @param UploadedFile $file
	 * @param string $path
	 *
	 * @return string
	 */
	private function uploadFile($file, $path)
	{
		$user = session()->get('username', '');

		$sourcePath = $path . '/' . $file->getClientOriginalName();

		$uploadFileName = $this->getCopyFilePath($sourcePath, $path);

		$this->storage->putFileAs('.', $file, $uploadFileName);

		$this->backup($user, $uploadFileName, 'upload');
	}

	/**
	 * Check if path is a directory
	 *
	 * @param string $path
	 *
	 * @return bool
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	private function isDirectory($path) {

		return $this->storage->getMimetype($path) === false;

	}

	/**
	 * Check if path is file
	 *
	 * @param string $path
	 *
	 * @return bool
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	private function isFile($path) {

		return $this->storage->getMimetype($path) !== false;

	}

	/**
	 * Copy file or directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return void
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	private function copy($sourcePath, $destinationPath) {

		if ($this->isFile($sourcePath)) {

			$this->copyFile($sourcePath, $destinationPath);

		} elseif ($this->isDirectory($sourcePath)) {

			$this->copyDirectory($sourcePath, $destinationPath);

		} else {

			throw new StorageException('Can not copy files or directories');

		}

	}

	/**
	 * Move file or directory
	 *
	 * @param string $sourcePath
	 * @param string $destinationPath
	 *
	 * @return void
	 * @throws StorageException
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	private function move($sourcePath, $destinationPath) {

		if ($this->isFile($sourcePath)) {

			$this->moveFile($sourcePath, $destinationPath);

		} elseif ($this->isDirectory($sourcePath)) {

			$this->moveDirectory($sourcePath, $destinationPath);

		} else {

			throw new StorageException('Can not move files or directories');

		}

	}

	/**
	 * Save Log
	 *
	 * @param string $user
	 * @param string $path
	 * @param string $event
	 *
	 * @return void
	 */

	private function log($user, $path, $event) {
		$date = date('Y-m-d H-i-s');
		$log = $user . ' ' . $path . ' ' . $event . ' ' . $date . PHP_EOL;
		$logpath = env('LOG_PATH', '/');
		if ($this->isFile($logpath)) {
			$this->storage->append($logpath, $log);
		}
	}

	/**
	 * Make backup
	 *
	 * @param string $path
	 *
	 * @return void
	 */

	private function backup($user, $path, $event) {
		$date = date('Y-m-d H-i-s');
		$info = $this->pathinfo($path);
		$log = $user . '-' . $info['basename'] . '-' . $event . '(' . $date . ')';

		$backup_path = env('LOG_PATH', '/');
		$destinationPath = $backup_path . $log;
		$this->copy($path, $destinationPath);
	}

}
