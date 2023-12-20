<?php $options = bc_import_csv__get_options(); ?>

<?php if(!$options->uploaded) { ?>
<div style="background:#fff; box-shadow: 0px 2px 1px rgba(0,0,0,0.2); border-radius: 5px">
	<div style="padding: 6px 10px;">
		<p style="margin:0"><strong>Upload CSV File</strong>: I can't yet find your CSV file, once you've uploaded the file in step 2, hit "Save Changes" below to give access to the file.</p>
	</div>
</div>
<?php } else { ?>

<input type="hidden" name="bc_import_csv__action" value="bc_import_csv__import">
<table cellspacing="0" style="width:100%; text-align: left; border: 1px solid #ddd; background: #fff">
	<thead style="background:#fff;">
		<tr>
			<th style="border-bottom: 1px solid #ddd;padding:10px"><?php $options->post_type; ?> Field</th>
			<th style="border-bottom: 1px solid #ddd;padding:10px">CSV Field</th>
		</tr>
	</thead>
	<tbody>
		<?php $csv_headers = bc_import_csv__get_csv_data_header(); ?>
		<?php foreach(bc_import_csv__get_post_fields() as $label => $value) { ?>
			<tr>
				<td style="padding:10px"><?php echo $label; ?></td>
				<td>
					<select style="width:100%;" name="import_field[<?php echo $value; ?>]" id="">
						<option value="0">Don't Map</option>
						<?php foreach($csv_headers as $header) { ?>
							<option value="<?php echo $header; ?>"><?php echo $header; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

<div style="padding: 10px; background: #fff; margin-top: 10px; border: 1px solid #ddd;">
	<input type="submit" name="do_import_csv" value="<?php echo __('Import CSV', 'bc_import_csv'); ?>" class="button button-secondary">
</div>



<?php } ?>

