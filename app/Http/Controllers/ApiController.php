<?php

namespace App\Http\Controllers;


use App\Exceptions\StorageException;
use App\Http\Requests\ContentInfo;
use App\Http\Requests\ContentList;
use App\Http\Requests\ContentPaste;
use App\Http\Requests\ContentSave;
use App\Http\Requests\ContentRemove;
use App\Http\Requests\ContentEdit;
use App\Http\Requests\ContentRename;
use App\Http\Requests\ChangeFileState;
use App\Http\Requests\DirectoryMake;
use App\Http\Requests\FileDownload;
use App\Http\Requests\FileUpload;
use App\Http\Requests\NewFile;
use App\Http\Requests\LockRequest;
use App\Models\StorageService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Session;

class ApiController extends Controller
{

	private $storageService;

	/**
	 * ApiController constructor.
	 *
	 * @param StorageService $storageService
	 */
	public function __construct(StorageService $storageService)
	{

		$this->storageService = $storageService;

	}

	/**
     * List of directory content
     *
     * @param ContentList $request
     *
     * @return JSON
     */
    public function list(ContentList $request)
    {

    	$content = $this->storageService->list($request);

	    return response()->json(
		    $content
	    );

    }

	/**
	 * Content info
	 *
	 * @param ContentInfo $request
	 *
	 * @return JSON
	 */
	public function info(ContentInfo $request)
	{

		$info = $this->storageService->info($request);

		return response()->json(
			$info
		);

	}

	/**
	 * Edit
	 *
	 * @param ContentEdit $request
	 *
	 * @return JSON
	 */
	public function edit(ContentEdit $request)
	{

		$result = $this->storageService->edit($request);
		$content = array('content'=>$result);

		return response()->json(
			$content
		);

	}
	/**
	 * Save
	 *
	 * @param ContentSave $request
	 *
	 * @return JSON
	 */
	public function save(ContentSave $request)
	{

		$result = $this->storageService->save($request);
		$return_val = array('result' => $result);
		return response()->json(
			$return_val
		);

	}

	/**
	 * makePublic
	 *
	 * @param ChangeFileState $request
	 *
	 * @return JSON
	 */
	public function makePublic(ChangeFileState $request)
	{

		$result = $this->storageService->makePublic($request);
		$return_val = array('result' => $result);
		return response()->json(
			$return_val
		);

	}

	/**
	 * makePrivate
	 *
	 * @param ChangeFileState $request
	 *
	 * @return JSON
	 */
	public function makePrivate(ChangeFileState $request)
	{

		$result = $this->storageService->makePrivate($request);
		$return_val = array('result' => $result);
		return response()->json(
			$return_val
		);

	}

	/**
	 * Remove
	 *
	 * @param ContentRename $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function rename(ContentRename $request)
	{

		$this->storageService->rename($request);

		return response()->json(
			[]
		);

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

		return $this->storageService->download($request);

	}

	/**
	 * Make directory
	 *
	 * @param DirectoryMake $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function makeDirectory(DirectoryMake $request)
	{

		$this->storageService->makeDirectory($request);

		return response()->json(
			[]
		);

	}

	/**
	 * Remove
	 *
	 * @param ContentRemove $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function remove(ContentRemove $request)
	{

		$this->storageService->remove($request);

		return response()->json(
			[]
		);

	}

	/**
	 * Paste
	 *
	 * @param ContentPaste $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function paste(ContentPaste $request)
	{

		$this->storageService->paste($request);

		return response()->json(
			[]
		);

	}

	/**
	 * Upload
	 *
	 * @param FileUpload $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function upload(FileUpload $request)
	{

		$this->storageService->upload($request);

		return response()->json(
			[]
		);

	}

	/**
	 * NewFile
	 *
	 * @param FileUpload $request
	 *
	 * @return JSON
	 * @throws StorageException
	 */
	public function new(NewFile $request)
	{

		$this->storageService->new($request);

		return response()->json(
			[]
		);

	}

	public function lockDelete(LockRequest $request) {
		$this->storageService->lockDelete($request);
		return response()->json(
			['message'=>'Successfully set']
		);
	}


	public function lockEdit(LockRequest $request) {
		$this->storageService->lockEdit($request);
		return response()->json(
			['message'=>'Successfully set']
		);
	}

}
