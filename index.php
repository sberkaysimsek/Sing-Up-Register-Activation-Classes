<!DOCTYPE html>
<html>
<head>
	<title>
		Üyelik Sınıfı - DORE
	</title>
	<meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
	<style type="text/css">
		@import url("css.css");
	</style>
</head>
<body>

	<div class="panel">
		<form action="" method="POST">
			<input type="textbox" name="kullanici_ad" placeholder="Kullanıcı Adınız" class="form_textbox"><div class="ara"></div>
			<input type="password" name="kullanici_sifre" placeholder="Şifreniz" class="form_textbox"><div class="ara"></div>
			<input type="submit" name="kullanici_giris" value="Giriş" class="form_button"><br>
		</form>
		<div class="links">
			<a href="kayit_ol.php" style="">Kayıt Ol ?</a>
			<a href="sifre_unut.php" style="">Şifrenizi mi Unuttunuz ?</a>
		</div>

		<?php 
		include "db_baglanti.php";
		require "classes.php";
		extract($_POST);

		if (isset($kullanici_giris)) 
		{	
			$giris = new uyelik_sinif('uyeler','uye_ad','uye_soyad','uye_ka','uye_sifre','uye_mail');// tablo değerleri
		 	$giris_deneme=$giris->giris_yap($db,$kullanici_ad,$kullanici_sifre);
		 	echo $giris_deneme;
		} 

		 ?>


	</div>


</body>
</html>
