<?php include('stubs/header.php'); ?>

<form method="post" enctype="multipart/form-data">
	<input type="file" id="url" name="media" />
	<button>Uppn</button>
	
	<p>
		<span>API key: <code><?php echo API_KEY; ?></code></span>
	</p>
</form>
<?php include('stubs/footer.php'); ?>