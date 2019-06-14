require('./bootstrap');

require('./helpers');

/**
 * Functions
 */

const getContent = (path = '.') => {

    startProcess();

    axios.get(
        '/api/list',
        {
            params: {
                path
            }

        }
    )
        .then(
            response => {

                stopProcess();

                $('#content_list tbody').empty();

                showInfo(response.data.info)
                showNavigation(response.data.quick_navigation)
                showDirectories(response.data.directories);
                showFiles(response.data.files);

                $('.tooltip-btn').tooltip();


            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}

const showDirectories = directoriesList => {

    $.each(directoriesList, function (index, directory) {

        $('#content_list tbody')
            .append(
                $('<tr />')
                    .append(
                        $('<td>')
                            .addClass('td-check')
                            .append(
                                $('<div />')
                                    .addClass('form-check')
                                    .append(
                                        $('<label />')
                                            .addClass('form-check-label')
                                            .append(
                                                $('<input />')
                                                    .addClass('form-check-input')
                                                    .attr(
                                                        {
                                                            type: 'checkbox',
                                                            value: directory.path
                                                        }
                                                    )
                                            )
                                            .append(
                                                $('<span />')
                                                    .addClass('form-check-sign')
                                                    .append(
                                                        $('<span />')
                                                            .addClass('check')
                                                    )
                                            )
                                    )
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass('td-type text-left')
                            .append(
                                $('<i />')
                                    .addClass('material-icons text-gray text-center')
                                    .text('folder')
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass('td-type text-left')
                            .append(
                                $('<i />')
                                    .addClass('material-icons text-gray text-center')
                                    .text(directory.locked == 'lock' ? 'lock' : '')
                            )
                    )
                    .append(
                        $('<td />')
                            .append(
                                $('<a />')
                                    .addClass('text-gray change-dir')
                                    .attr('href', directory.path)
                                    .text(directory.name)
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass('url')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-size text-center')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-modified text-center')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-actions text-right')
                            .append(
                                $('<button />')
                                    .addClass('btn btn-info btn-sm ml-2 tooltip-btn info-btn')
                                    .attr('type', 'button')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Show Info')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('info')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-warning btn-sm ml-2 tooltip-btn rename-btn')
                                    .attr('type', 'button')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Rename')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('question_answer')
                                    )
                            )
                    )
            )

    });

}

const showFiles = filesList => {

    $.each(filesList, function (index, file) {

        $('#content_list tbody')
            .append(
                $('<tr />')
                    .append(
                        $('<td>')
                            .addClass('td-check')
                            .append(
                                $('<div />')
                                    .addClass('form-check')
                                    .append(
                                        $('<label />')
                                            .addClass('form-check-label')
                                            .append(
                                                $('<input />')
                                                    .addClass('form-check-input')
                                                    .attr(
                                                        {
                                                            type: 'checkbox',
                                                            value: file.path
                                                        }
                                                    )
                                            )
                                            .append(
                                                $('<span />')
                                                    .addClass('form-check-sign')
                                                    .append(
                                                        $('<span />')
                                                            .addClass('check')
                                                    )
                                            )
                                    )
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass('td-type text-left file-visibility')
                            .append(
                                $('<i />')
                                    .addClass('material-icons text-gray text-center')
                                    .text(file.public_status == 'public' ? 'cloud_queue' : 'cloud_off')
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass('td-type text-left lock-status')
                            .append(
                                $('<i />')
                                    .addClass('material-icons text-gray text-center')
                                    .text(file.lock_status == 2 ? 'lock' : file.lock_status == 1 ? 'delete_forever' : 'lock_open')
                            )
                    )
                    .append(
                        $('<td />')
                            .addClass()
                            .text(file.name)
                    )
                    .append(
                        $('<td />')
                            .addClass('td-url')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-size text-center')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-modified text-center')
                            .text('')
                    )
                    .append(
                        $('<td />')
                            .addClass('td-actions text-right')
                            .append(
                                $('<button />')
                                    .addClass('btn btn-info btn-sm tooltip-btn visibility-btn')
                                    .attr('id', 'btn_visibility')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', file.public_status == 'public' ? 'Make Private' : 'Make Public')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text(file.public_status == 'public' ? 'cloud_off' : 'cloud_queue')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-danger btn-sm ml-2 tooltip-btn delete-lock-btn')
                                    .attr('id', 'btn_lock_delete')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Lock Delete')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('delete_forever')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-danger btn-sm ml-2 tooltip-btn edit-lock-btn')
                                    .attr('id', 'btn_lock_edit')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Lock Edit')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('lock')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-primary btn-sm ml-2 tooltip-btn edit-btn')
                                    .attr('id', 'btn_edit')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Edit')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('edit')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-success btn-sm ml-2 tooltip-btn download-btn')
                                    .attr('type', 'button')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Download')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('save_alt')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-info btn-sm ml-2 tooltip-btn info-btn')
                                    .attr('type', 'button')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Show Info')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('info')
                                    )
                            )
                            .append(
                                $('<button />')
                                    .addClass('btn btn-warning btn-sm ml-2 tooltip-btn rename-btn')
                                    .attr('type', 'button')
                                    .attr('data-toggle', 'tooltip')
                                    .attr('data-placement', 'top')
                                    .attr('title', 'Rename')
                                    .append(
                                        $('<i />')
                                            .addClass('material-icons')
                                            .text('question_answer')
                                    )
                            )
                    )
            )

    });

}

const showNavigation = navigationList => {

    $('#quick_navigation .breadcrumb').empty();

    $.each(navigationList, function (index, directory) {

        $('#quick_navigation .breadcrumb')
            .append(
                $('<li />')
                    .addClass('breadcrumb-item')
                    .append(
                        $('<a />')
                            .addClass('change-dir text-primary font-weight-bold')
                            .attr('href', directory.path)
                            .text(directory.name)
                    )
            )
    });

}

const showInfo = dirInfo => {

    $('#select_all_files').prop('checked', false);

    $('#current_directory').data('path', dirInfo.dirPath);

    $('#directory_name').html(dirInfo.dirName);

    $('#dir_count').html(dirInfo.dirCount);

    $('#files_count').html(dirInfo.filesCount);

}

const makeDirectory = () => {

    const form = $('#make_directory_frm');

    const currentDirectory = $('#current_directory').data('path');

    const directoryName = $('input[name="directory"]', form).val();

    if (directoryName.length === 0) {

        showError('Provide correct directory name.');

        return;

    }

    startProcess();

    axios.post(
        '/api/make-directory',
        qs.stringify(
            {
                'path': currentDirectory + '/' + directoryName,
            }
        )
    )
        .then(
            () => {

                stopProcess();

                showMessage('Done.')
                    .then(
                        () => getContent(currentDirectory)
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}

const renameContent = path => {

    const form = $('#rename_content_frm');

    const name = $('input[name="name"]', form).val();

    if (name.length === 0) {

        showError('Provide correct name.');

        return;

    }

    startProcess();

    axios.post(
        '/api/rename',
        qs.stringify(
            {
                'path': path,
                'name': name
            }
        )
    )
        .then(
            () => {

                stopProcess();

                showMessage('Done.')
                    .then(
                        () => {

                            const currentDirectory = $('#current_directory').data('path');

                            getContent(currentDirectory);

                        }
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}

const createNewFile = (path, content) => {
    const form = $('#new_file_content_frm');

    const name = $('input[name="name"]', form).val();

    if (name.length === 0) {

        showError('Provide correct name.');

        return;

    }
    file = new Blob([content], { type: 'text/plain' });
    

    let requestData = new FormData();

    requestData.append('file', file);
    requestData.append('path', path);
    requestData.append('name', name);

    startProcess();
    
    axios.post(
        '/api/new',
        requestData
    )
        .then(
            () => {

                stopProcess();

                showMessage('Done.')
                    .then(
                        () => {

                            const currentDirectory = $('#current_directory').data('path');

                            getContent(currentDirectory);

                        }
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )
}

const removeContent = () => {

    let pathToRemove = [];

    $('#content_list tbody input[type="checkbox"]:checked').each(function () {

        pathToRemove.push($(this).val());

    });

    if (pathToRemove.length === 0) {

        showError('Select files or directories.');

        return;

    }

    startProcess();

    axios.post(
        '/api/remove',
        qs.stringify(
            {
                'path_list': pathToRemove,
            }
        )
    )
        .then(
            () => {

                stopProcess();

                showMessage('Done.')
                    .then(
                        () => {

                            const currentDirectory = $('#current_directory').data('path');

                            getContent(currentDirectory);

                        }
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}

const pasteContent = () => {

    const pasteContent = JSON.parse(sessionStorage.getItem('paste_content'));

    if (!pasteContent || !pasteContent.source_path_list) {

        showError('No files or directories were selected.');

        return;

    }

    const currentDirectory = $('#current_directory').data('path');

    startProcess();

    axios.post(
        '/api/paste',
        qs.stringify(
            {
                'operation': pasteContent.operation,
                'source_path_list': pasteContent.source_path_list,
                'destination_path': currentDirectory
            }
        )
    )
        .then(
            () => {

                stopProcess();

                sessionStorage.clear();

                showMessage('Done.')
                    .then(
                        () => getContent(currentDirectory)
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}

const uploadFiles = (files = null) => {

    if (files === null) {

        const form = $('#upload_files_frm');

        files = $('input[type="file"]', form)[0].files;

    }

    let requestData = new FormData();

    if (files.length === 0) {

        showError('Select files for uploading.');

        return;

    }

    for (let i = 0; i < files.length; i++) {

        requestData.append('files_list[]', files[i]);

    }

    const currentDirectory = $('#current_directory').data('path');

    requestData.append('path', currentDirectory);

    startProcess();

    axios.post(
        '/api/upload',
        requestData
    )
        .then(
            () => {

                stopProcess();

                showMessage('Done')
                    .then(
                        () => getContent(currentDirectory)
                    );

            }
        )
        .catch(
            error => {

                stopProcess();

                showError(error.response.data.message);

            }
        )

}


/**
 * Document ready
 */
$(document).ready(function () {


    /**
     * Upload Files
     */
    $(document).on('change', '#local_file', function (event) {

        event.preventDefault();

        $('input[name="local_file"]').val($(this)[0].files.length + ' files selected');

    });

    

    $(document).on('click', 'input[name="local_file"]', function (event) {

        event.preventDefault();

        $('#local_file').trigger('click');

    });


    /**
     * Change to public / private
     */
    $(document).on('click', '.visibility-btn', function(event) {
        event.preventDefault();
        const row = $(this).parents('tr');

        let path = row.find('input[type="checkbox"]').val();

        const button = $(this);
        file_status = $('i', button).text();

        url = file_status == 'cloud_off' ? '/api/make-private' : '/api/make-public';

        showSpinner(button);

        axios.get(
            url,
            {
                params: {
                    path
                }
            }
        )
            .then(
                response => {

                    hideSpinner(button);
                    showMessage(response.data.message);

                    $('.file-visibility i', row).text(file_status);
                    $('i', button).text(file_status == 'cloud_off' ? 'cloud_queue' : 'cloud_off');
                }
            )
            .catch(
                error => {

                    hideSpinner(button);

                    showError(error.response.data.message);

                }
            )
    });


    /**
     * Lock file
     */

    $(document).on('click', '.delete-lock-btn', function(event) {
        event.preventDefault();
        const row = $(this).parents('tr');

        let path = row.find('input[type="checkbox"]').val();

        const button = $(this);

        showSpinner(button);

        axios.get(
            '/api/lock-delete',
            {
                params: {
                    path
                }
            }
        )
            .then(
                response => {

                    hideSpinner(button);
                    showMessage(response.data.message);
                    if ($('.lock-status i', row).text() == 'lock_open')
                        $('.lock-status i', row).text('delete_forever');

                }
            )
            .catch(
                error => {

                    hideSpinner(button);

                    showError(error.response.data.message);

                }
            )
    });

    $(document).on('click', '.edit-lock-btn', function(event) {
        event.preventDefault();
        const row = $(this).parents('tr');

        let path = row.find('input[type="checkbox"]').val();

        const button = $(this);

        showSpinner(button);

        axios.get(
            '/api/lock-edit',
            {
                params: {
                    path
                }
            }
        )
            .then(
                response => {

                    hideSpinner(button);
                    showMessage(response.data.message);
                    if ($('.lock-status i', row).text() != 'lock')
                        $('.lock-status i', row).text('lock');

                }
            )
            .catch(
                error => {

                    hideSpinner(button);

                    showError(error.response.data.message);

                }
            )
    });

    /**
     * Search on the page
     */
    $('#search_form input[type="text"]').on('keyup', function (event) {

        // Clear form
        if (event.keyCode === 27) {

            $('#clear_search').trigger('click');

        }

        // Toggle visibility of the clear button
        if ($(this).val().length === 0) {

            $('#clear_search').addClass('d-none');

        } else {

            $('#clear_search').removeClass('d-none');

        }

    });

    $('#clear_search').on('click', function () {

        $('#search_form input[type="text"]').val('');

        $('#search_form').trigger('submit');

        $(this).addClass('d-none');

    });

    $('#search_form').on('submit', function (event) {

        event.preventDefault();

        const search = $('input[type="text"]', $(this)).val().toLowerCase();

        $('#content_list tbody tr').each(function () {

            const fileName = $('td:eq(2)', $(this)).text();

            const notFound = (fileName.toLowerCase().indexOf(search) === -1);

            if (notFound && search.length > 0) {
                $(this).hide();
            } else {
                $(this).show();
            }

        });

    });


    /**
     * Handle actions
     */
    $(document).on('click', '.change-dir', function (event) {

        event.preventDefault();

        const directory = $(this).attr('href');

        getContent(directory);

    });

    $(document).on('click', '.info-btn', function (event) {

        event.preventDefault();

        const row = $(this).parents('tr');

        let path = row.find('input[type="checkbox"]').val();

        const button = $(this);

        showSpinner(button);

        axios.get(
            '/api/info',
            {
                params: {
                    path
                }
            }
        )
            .then(
                response => {

                    hideSpinner(button);
                    url = 'https://s3.amazonaws.com/r.lake/'+path;
                    row.find('.td-url').html('<a href="'+url+'">'+url+'</a>');
                    row.find('.td-size').text(response.data.size);
                    row.find('.td-modified').text(response.data.modified);

                }
            )
            .catch(
                error => {

                    hideSpinner(button);

                    showError(error.response.data.message);

                }
            )


    });

    $(document).on('click', '.rename-btn', function (event) {

        event.preventDefault();

        const row = $(this).parents('tr');

        const path = row.find('input[type="checkbox"]').val();
        const oldName = row.find('td:eq(2)').text();

        const renameContentForm = $('<form />')
            .attr('id', 'rename_content_frm')
            .append(
                $('<div />')
                    .addClass('form-group')
                    .append(
                        $('<input />')
                            .addClass('form-control')
                            .attr(
                                {
                                    'type': 'text',
                                    'name': 'name',
                                    'autocomplete': 'off',
                                    'placeholder': 'New name',
                                    'autofocus': true
                                }
                            )
                            .val(oldName)
                    )
            );

        showEdit('Rename', renameContentForm.get(0))
            .then(
                value => {

                    if (value) {
                        renameContent(path);
                    }

                }
            )


    });

    $(document).on('click', '.download-btn', function (event) {

        event.preventDefault();

        const path = $(this).parents('tr').find('input[type="checkbox"]').val();

        window.open('/api/download?path=' + path, '_blanc');

    });

    $(document).on('submit', '#rename_content_frm, #make_directory_frm, #upload_files_frm', '#new_file_content_frm', function (event) {

        event.preventDefault();

        $('.swal-button--confirm').trigger('click')

    });


    $('#select_all_files').on('click', function () {

        $('input[type="checkbox"]').prop('checked', $(this).prop('checked'))

    });

    $('#make_directory_btn').on('click', function (event) {

        event.preventDefault();

        const makeDirectoryForm = $('<form />')
            .attr('id', 'make_directory_frm')
            .append(
                $('<div />')
                    .addClass('form-group')
                    .append(
                        $('<input />')
                            .addClass('form-control')
                            .attr(
                                {
                                    'type': 'text',
                                    'name': 'directory',
                                    'autocomplete': 'off',
                                    'placeholder': 'Directory name',
                                    'autofocus': true
                                }
                            )
                            .val('')
                    )
            );

        showEdit('Make Directory', makeDirectoryForm.get(0))
            .then(
                value => {

                    if (value) {
                        makeDirectory()
                    }

                }
            )

    });

    $('#copy_btn').on('click', function (event) {

        event.preventDefault();

        let pathToCopy = [];

        $('#content_list tbody input[type="checkbox"]:checked').each(function () {

            pathToCopy.push($(this).val());

        });

        const pasteContent = {
            'operation': 'copy',
            'source_path_list': pathToCopy
        };

        sessionStorage.setItem('paste_content', JSON.stringify(pasteContent));

    });

    $('#cut_btn').on('click', function (event) {

        event.preventDefault();

        let pathToCut = [];

        $('#content_list tbody input[type="checkbox"]:checked').each(function () {

            pathToCut.push($(this).val());

        });

        const pasteContent = {
            'operation': 'cut',
            'source_path_list': pathToCut
        };

        sessionStorage.setItem('paste_content', JSON.stringify(pasteContent));

    });

    $('#paste_btn').on('click', function (event) {

        event.preventDefault();

        showConfirmation('You are trying to paste content.')
            .then(
                value => {

                    if (value) {
                        pasteContent()
                    }

                }
            )

    });

    $('#remove_btn').on('click', function (event) {

        event.preventDefault();

        showConfirmation('You are trying to delete content.')
            .then(
                value => {

                    if (value) {
                        removeContent()
                    }

                }
            )

    });

    $('#upload_file_btn').on('click', function (event) {

        event.preventDefault();

        const uploadFileForm = $('<form />')
            .attr(
                {
                    enctype: 'multipart/form-data',
                    id: 'upload_files_frm'
                }
            )
            .append(
                $('<div />')
                    .addClass('form-group')
                    .append(
                        $('<input />')
                            .addClass('form-control-plaintext')
                            .attr('type', 'text')
                            .val('Select files for uploading or drag them directly to the files list')
                    )
            )
            .append(
                $('<div />')
                    .addClass('form-group')
                    .append(
                        $('<input />')
                            .addClass('form-control')
                            .attr(
                                {
                                    type: 'text',
                                    name: 'local_file',
                                    placeholder: 'Click to select files',
                                    autocomplete: 'off'
                                }
                            )
                    )
                    .append(
                        $('<input />')
                            .addClass('d-none')
                            .attr(
                                {
                                    type: 'file',
                                    id: 'local_file',
                                    multiple: true
                                }
                            )
                    )
            );

        showEdit('Upload Files', uploadFileForm.get(0))
            .then(
                value => {

                    if (value) {
                        uploadFiles();
                    }

                }
            )

    });

    /**
     * Drug & drop files
     */
    $('.content').on('dragenter', function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('body')
            .append(
                $('<div />')
                    .addClass('drop-container process-container')
                    .html('Drop files to start upload')
            )

    });

    $(document).on('dragenter dragover dragleave', '.drop-container', function (event) {

        event.preventDefault();
        event.stopPropagation();

    });

    $(document).on('drop', '.drop-container', function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('.drop-container').remove();

        uploadFiles(event.originalEvent.dataTransfer.files)

    });

    /**
     * Start application
     */
    getContent();

    $(document).on('click', '#save_btn', function (event) {
        path = $('#modal_path').val();
        content = window.editor.getNativeEditorValue();
        $(this).next().click();
        

        $.ajax({
            url: '/api/save',
            type: 'POST',
            data: {
                path: path,
                content: content
            },
            sucess: function(data){
                alert(data);
            }
        })
    });

    $(document).on('click', '#btn_edit', function (event) {

        // event.preventDefault();
        console.log('edit');
        startProcess();

        const row = $(this).parents('tr');

        const path = row.find('input[type="checkbox"]').val();

        axios.get(
            '/api/edit',
            {
                params: {
                    path
                }

            }
        )
            .then(
                response => {

                    stopProcess();
                    $('#edit').html('<input type="hidden" id="modal_path"><div id="editor"></div><button id="save_btn">Save</button>');
                    $('#editor').html(response.data.content);
                    $('#edit').modal();
                    $('#modal_path').val(path);
                    var editor = new Jodit('#editor', {
                        textIcons: false,
                        iframe: false,
                        iframeStyle: '*,.jodit_wysiwyg {color:red;}',
                        height: 460,
                        defaultMode: Jodit.MODE_WYSIWYG,
                        observer: {
                            timeout: 100
                        },
                        uploader: {
                            url: 'https://xdsoft.net/jodit/connector/index.php?action=fileUpload'
                        },
                        filebrowser: {
                            ajax: {
                                url: 'https://xdsoft.net/jodit/connector/index.php'
                            }
                        },
                        commandToHotkeys: {
                            'openreplacedialog': 'ctrl+p'
                        }                        
                    });

                    window.editor = editor
                }
            )
            .catch(
                error => {

                    stopProcess();

                    showError(error.response.data.message);

                }
            )
       

    });

    $(document).on('click', '#save_new_btn', function (event) {
        var path = $('#current_directory').data('path');
        var content = window.editor.getNativeEditorValue();
        $(this).next().click();
        const newFileNameForm = $('<form />')
            .attr('id', 'new_file_content_frm')
            .append(
                $('<div />')
                    .addClass('form-group')
                    .append(
                        $('<input />')
                            .addClass('form-control')
                            .attr(
                                {
                                    'type': 'text',
                                    'name': 'name',
                                    'autocomplete': 'off',
                                    'placeholder': 'New name',
                                    'autofocus': true
                                }
                            )
                            .val('')
                    )
            );

        showEdit('New File Name', newFileNameForm.get(0))
            .then(
                value => {

                    if (value) {
                        createNewFile(path, content);
                    }

                }
            )
    });

    $(document).on('click', '#create_new_file_btn', function (event) {
        event.preventDefault();
        $('#edit').html('<div id="editor"></div><button id="save_new_btn">Save</button>');
        $('#editor').html('');
        $('#edit').modal();
        var editor = new Jodit('#editor', {
            textIcons: false,
            iframe: false,
            iframeStyle: '*,.jodit_wysiwyg {color:red;}',
            height: 460,
            defaultMode: Jodit.MODE_WYSIWYG,
            observer: {
                timeout: 100
            },
            uploader: {
                url: 'https://xdsoft.net/jodit/connector/index.php?action=fileUpload'
            },
            filebrowser: {
                ajax: {
                    url: 'https://xdsoft.net/jodit/connector/index.php'
                }
            },
            commandToHotkeys: {
                'openreplacedialog': 'ctrl+p'
            }                        
        });

        window.editor = editor
    })

});