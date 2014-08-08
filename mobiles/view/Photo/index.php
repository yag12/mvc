포토 리스트

<?php if(!empty($rows)): ?>
<table>
<tr>
	<td>
		<?php foreach($rows as $row): ?>
			<div style="width:100px; height:130px; overflow:hidden; float:left; margin:8px;">
				<div style="width:100px; height:100px; overflow:hidden;">
					<img src="<?php echo DEFAULT_URL; ?>/image/photo/<?php echo $row['photo_name']; ?>" />
				</div>
				<div style="height:30px; text-align:center; padding-top:2px;">
					<input type="button" value="XXX" />
					<input type="button" value="OOO" />
				</div>
			</div>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<td>
		<ul id="pagination-digg">
		<?php echo $this->partials('paginator.php', $paginator); ?>
		</ul>
	</td>
</tr>
</table>
<?php endif; ?>
