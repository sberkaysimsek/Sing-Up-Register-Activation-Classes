<!DOCTYPE html>
<html>
<head>
	<title>
		Aktivasyon - DORE
	</title>
	<meta charset="utf-8">
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
	<style type="text/css">
		@import url("css.css");
	</style>
</head>
<body>

	<div class="panel">
		<h3  id = "aktivasyon_h3">E-Mailinize gelen aktivasyon kodunu girin.</h3>
		<form action="" method="POST">
			<input type="textbox" name="aktivasyon_kod" placeholder="Aktivasyon Kodu" class="form_textbox"><div class="ara"></div>
			<input type="submit" name="kod_gonder" value="Gönder" class="form_button"><br>
		</form>
		<div class="links">
			<a href="index.php" style="">- Çıkış -</a>
		</div>
		<?php

			extract($_POST);
			require("classes.php");
			include 'db_baglanti.php';
			$kuki_id = $_COOKIE['kuki_id'];
			$hata_kod = "";
			if (isset($kod_gonder)) 
			{
				$kod_nesne = new uyelik_sinif('uyeler','uye_ad','uye_soyad','uye_ka','uye_sifre','uye_mail','aktivasyon_kod','hata_no');
				$yaz = $kod_nesne->aktivasyon_sorgu($db,$kuki_id,$aktivasyon_kod,$hata_kod);
				echo $yaz;
			}

		 ?>

	</div>


</body>
</html>
