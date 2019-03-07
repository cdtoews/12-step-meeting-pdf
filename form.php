<?php

add_shortcode('meeting-pdf-form', function(){

	//security
	if (!is_user_logged_in()) {
		return 'please log in';
	} elseif (!current_user_can('edit_posts')) {
		return 'you do not have access to view this page';
	}

	return '
	<h3>Generate PDF</h3>
	<form method="get" class="form-horizontal" action="' . admin_url('admin-ajax.php') . '">
	  <input type="hidden" name="action" value="step_pdf">
	<fieldset>

<!-- Form Name -->
<legend>Form Name</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="header_text">Header title</label>
  <div class="col-md-6">
  <input id="header_text" name="header_text" type="text" value="Blah AA Meeting" class="form-control input-md">

  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="font_size">Font Size</label>
  <div class="col-md-2">
  <input id="font_size" name="font_size" type="text" value="8" class="form-control input-md">

  </div>

	<!-- Text input-->
	<div class="form-group">
	  <label class="col-md-4 control-label" for="margin">margin</label>
	  <div class="col-md-2">
	  <input id="margin" name="margin" type="text" value="10" class="form-control input-md">

	  </div>

		<!-- Text input-->
		<div class="form-group">
			<label class="col-md-4 control-label" for="cover_post_id">Post/Page ID containing HTML for pdf</label>
			<div class="col-md-2">
			<input id="cover_post_id" name="cover_post_id" type="text" class="form-control input-md">

			</div>

</div>

<!-- Multiple Radios (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="column_count">Column count</label>
  <div class="col-md-4">
    <label class="radio-inline" for="column_count-0">
      <input type="radio" name="column_count" id="column_count-0" value="1" checked="checked">
      1
    </label>
    <label class="radio-inline" for="column_count-1">
      <input type="radio" name="column_count" id="column_count-1" value="2">
      2
    </label>
    <label class="radio-inline" for="column_count-2">
      <input type="radio" name="column_count" id="column_count-2" value="3">
      3
    </label>
    <label class="radio-inline" for="column_count-3">
      <input type="radio" name="column_count" id="column_count-3" value="4">
      4
    </label>
    <label class="radio-inline" for="column_count-4">
      <input type="radio" name="column_count" id="column_count-4" value="5">
      5
    </label>
    <label class="radio-inline" for="column_count-5">
      <input type="radio" name="column_count" id="column_count-5" value="6">
      6
    </label>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="column_padding">Column Padding</label>
  <div class="col-md-4">
  <input id="column_padding" name="column_padding" type="text" value="5" class="form-control input-md">
  <span class="help-block">padding between columns</span>
  </div>
</div>


<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">Generate PDF</button>
  </div>
</div>

</fieldset>
</form>
	';
});
