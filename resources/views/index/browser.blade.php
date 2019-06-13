@extends('layouts.browser')


@section('title')
	AWS File Manager
@endsection


@section('content')


	<div class="row">
		<div class="col-12">

			<div class="card">

				<div class="card-body">

					<div class="table-responsive">

						<table id="content_list" class="table table-hover">

							<thead>

								<tr>

									<th id="current_directory" colspan="8">

										<div class="float-left">
											Current directory: <strong id="directory_name"></strong>
										</div>

										<div class="float-right">
											Directories: <strong id="dir_count"></strong>
											&nbsp
											Files: <strong id="files_count"></strong>
										</div>

									</th>

								</tr>

								<tr>

									<th class="td-check">

										<div class="form-check">

											<label class="form-check-label">
												<input id="select_all_files" class="form-check-input" type="checkbox" >
												<span class="form-check-sign">
				                                    <span class="check"></span>
												</span>
											</label>

										</div>

									</th>

									<th colspan="7">

										<div class="navbar-wrapper">

											<nav id="quick_navigation" aria-label="breadcrumb">
												<ol class="breadcrumb">
												</ol>
											</nav>

										</div>

									</th>

								</tr>
							</thead>

							<tbody></tbody>

						</table>

					</div>

				</div>

			</div>

		</div>

	</div>
	<div id="edit" class="modal">
		<input type="hidden" id="modal_path">
		<div id="editor">
		</div>
		<button id="save_btn">Save</button>
	</div>
	

@endsection