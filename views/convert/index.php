<div class="row">
	<div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="my-temp fab-fw"></i> Gcode Temp Converter <span> >
				Convert</span>
		</h1>
	</div>
	<div class="col-xs-6 col-sm-8 col-md-8 col-lg-8 text-align-right">
	

			<a
				href="<?php  echo site_url('gcodeTempConverter/edit/'.$_object_id)?>"
				class="btn btn-primary pull-right"> <i class="icon-fab-manager"></i> Back to
				files
			</a>
	
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="well no-border">


			<div class="row">
				<div class="col-sm-6">

					<div class="form-group">
						<div class="col-md-12">
							<h5>Name</h5>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<input type="text" id="name" name="name" class="form-control"
								value="<?php echo $_file->raw_name; ?>" />
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							<h5>Note</h5>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<textarea id="note" name="note" class="form-control" rows="2"><?php echo $_file->note; ?></textarea>
						</div>
					</div>

				</div>
                	
                	<?php if(!$is_stl && strtolower($_file->file_ext) != '.nc'): ?>
	                	<div class="col-sm-6">
					<div class="form-group">
						<div class="col-md-12">
							<p>
							
							
							<h5>
								Model size: <span class="text-info"><?php echo $dimesions; ?></span>
							</h5>
							</p>
							<p>
							
							
							<h5>
								Filament used: <span class="text-info"><?php echo $filament; ?></span>
							</h5>
							</p>
							<p>
							
							
							<h5>
								Estimated time print: <span class="text-info"><?php echo $estimated_time; ?></span>
							</h5>
							</p>
							<p>
							
							
							<h5>
								Layers: <span class="text-info"><?php echo $number_of_layers; ?></span>
							</h5>
							</p>



						</div>

					</div>

				</div>
                	<?php endif; ?>
                	
                </div>



			<div class="row">
				<div class="col-sm-12 col-md-6">



					<div class="col-md-12">
						<h5 id="file-content-title">
							Loading content.. <i class="fa fa-spin fa-spinner"></i>
						</h5>
					</div>


					<div class="col-md-12">
						<pre class="well" id="editor" style="display: none;"></pre>
						<div>
							<button class="btn btn-primary" id="submit">
								<i class="fa fa-refresh"></i>&nbsp;Convert File
							</button>
							
						</div>
					</div>



				</div>
				<div class="col-sm-12 col-md-6">
					<table
						class="table table-striped table-hover table-bordered smart-form editableTable"
						id="filament_table">
						<thead>
							<tr>
								<th rowspan="2"></th>
								<th rowspan="2">Name</th>
								<th colspan="2" class="hidden-xs">Bed</th>
								<th colspan="2" class="hidden-xs">Extruder</th>
								<th></th>


							</tr>
							<tr>
								<th class="hidden-xs">First Layer</th>
								<th class="hidden-xs">Other Layers</th>
								<th class="hidden-xs">First Layer</th>
								<th class="hidden-xs">Other Layers</th>
								<th></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="7"><button class="btn btn-primary" id="save-fil">
										<i class="fa fa-save"></i>&nbsp;Save List
									</button>


									<button class="btn btn-primary" id="new-row">
										<i class="fa fa-bars"></i>&nbsp;New Row
									</button></td>
							</tr>

						</tfoot>
						<tbody>

							<tr class="fil">
								<td><label class="radio"> <input type="radio"
										name="checkbox-inline"><i></i></label></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>



							</tr>


						</tbody>
					</table>
				</div>
			</div>

			



		</div>
	</div>
</div>
