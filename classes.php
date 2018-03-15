<?php 
	
	class uyelik_sinif
	{	
		public $tablo_uye_ad;
		public $tablo_uye_soyad;
		public $tablo_uye_kullanici_ad;
		public $tablo_uye_kullanici_sifre;
		public $tablo_uye_mail;
		public $t_aktivasyon_kod;


		function __construct($tablo_ad,$tablo_uye_ad,$tablo_uye_soyad,$tablo_uye_kullanici_ad,$tablo_uye_kullanici_sifre,$tablo_uye_mail,$aktivasyon_kod,$tablo_hata_no)
		{	
			$this->tablo_ad=$tablo_ad;
			$this->t_kullanici_ad=$tablo_uye_ad;
			$this->t_kullanici_soyad=$tablo_uye_soyad;
			$this->t_kullanici_kad=$tablo_uye_kullanici_ad;
			$this->t_kullanici_sifre=$tablo_uye_kullanici_sifre;
			$this->t_kullanici_mail=$tablo_uye_mail;
			$this->t_aktivasyon_kod = $aktivasyon_kod;
			$this->t_hata_no= $tablo_hata_no;
		}
		function kayit_ol($db,$uye_ad,$uye_soyad,$uye_kullanici_ad,$uye_kullanici_sifre,$uye_kullanici_mail,$aktivasyon_kod)
		{
			if (empty($uye_ad) || empty($uye_soyad) || empty($uye_kullanici_ad) || empty($uye_kullanici_sifre) || empty($uye_kullanici_mail))  
			{
				return "Lütfen Boş Alan Bırakmayınız.";
			}
			else
			{
				if (strlen($uye_kullanici_ad>15))
				{
					return "Kullanıcı adınız 15 karakteri geçemez.";
				}
				else
				{
					$kad_kontrol =$db->prepare("SELECT $this->t_kullanici_kad FROM $this->tablo_ad WHERE $this->t_kullanici_kad=?");
					$kad_kontrol->execute(array($uye_kullanici_ad));
					$kad_kontrol= $kad_kontrol->fetch();
					if ($kad_kontrol) 
					{
						return "Böyle Bir Kullanıcı Adı Mevcut.";
					}
					else
					{
						if (strlen($uye_kullanici_sifre)<7) 
						{
							return "Şifreniz 7 karakterden küçük olamaz.";
						}
						else
						{
							if (!filter_var($uye_kullanici_mail, FILTER_VALIDATE_EMAIL)) 
							{
								return "E-Posta adresinizin formatı hatalı.";
							}
							else
							{
								$kmail_kontrol =$db->prepare("SELECT $this->t_kullanici_mail FROM $this->tablo_ad WHERE $this->t_kullanici_mail=?");
								$kmail_kontrol->execute(array($uye_kullanici_mail));
								$kmail_kontrol=$kmail_kontrol->fetch();
								
								if ($kmail_kontrol) 
								{
									return "Böyle Bir Mail adresi Mevcut.";
								}
								else
								{
									$kisi_ekle = $db->query("INSERT INTO $this->tablo_ad($this->t_kullanici_ad,$this->t_kullanici_soyad,$this->t_kullanici_kad,$this->t_kullanici_sifre,$this->t_kullanici_mail,$this->t_aktivasyon_kod) VALUES ('$uye_ad','$uye_soyad','$uye_kullanici_ad','$uye_kullanici_sifre','$uye_kullanici_mail','$aktivasyon_kod')");
									if ($kisi_ekle) 
									{	
										$kuki =$uye_kullanici_mail;
										setcookie('kuki_id',$kuki,time()+3600);
										header("Location:aktivasyon.php");

										//
										$kod_sorgula = $db->prepare("SELECT $this->t_aktivasyon_kod FROM $this->tablo_ad WHERE $this->t_kullanici_mail = ? ");
										$kod_sorgula ->execute(array($kuki_id));
										$kod_sorgula=$kod_sorgula->fetchAll();
										foreach ($kod_sorgula as $key) 
										{
											$mesaj = $key['aktivasyon_kod'];
										}
										$konu = "Aktivasyon Kod";
										$header = "From : sberkaysimsek_95@hotmail.com"."Content-Type:text/html; Charset=iso-8859-9\r\n";
										mail($this->t_kullanici_mail,$konu,$mesaj,$header);
									}
									else
									{
										return "Kayıt Başarısız";
									}

								}
							}
						}
					}
				}
			}
		}

		function giris_yap($db,$kullanici_ad,$kullanici_sifre)
		{
			if (empty($kullanici_sifre) or empty($kullanici_ad)) 
			{
				return "Lütfen boş alan bırakmayınız.";
			}
			else{
				$ka_sorgu = $db->prepare("SELECT * FROM $this->tablo_ad WHERE $this->t_kullanici_kad = ? && $this->t_kullanici_sifre=?");
				$ka_sorgu->execute(array($kullanici_ad,$kullanici_sifre));
				$ka_sorgu=$ka_sorgu->fetch();
				if ($ka_sorgu) 
				{
					
					header("refresh:3;url=kayit_ol.php"); // istenilen yere atlar.
					return "Giriş başarılı.Anasayfaya yönlendiriliyorsunuz."; 
				}
				else{
					return "Hatalı kullanıcı adı veya şifre.";
				}
			}
		}

		function aktivasyon_sorgu($db,$kuki_id,$aktiv_kod,$hata_kod)
		{	
			if (empty($aktiv_kod)) 
			{
				return "Kod alanı doldurmalısınız.";
			}
			else
			{
				$kod_sorgula = $db->prepare("SELECT $this->t_aktivasyon_kod FROM $this->tablo_ad WHERE $this->t_kullanici_mail = ? ");
				$kod_sorgula ->execute(array($kuki_id));
				$kod_sorgula=$kod_sorgula->fetchAll();
				if ($kod_sorgula) 
				{	
					foreach ($kod_sorgula as $key) 
					{
						if($key['aktivasyon_kod']==$aktiv_kod)
						{
							$update_kod = $db->prepare("UPDATE $this->tablo_ad SET $this->t_aktivasyon_kod =? WHERE $this->t_aktivasyon_kod = ?");
							$update_kod ->execute(array('ONAYLANDI',$aktiv_kod));

							if ($update_kod) 
							{					
								$guncelle =$db->prepare("UPDATE $this->tablo_ad SET $this->t_hata_no = ? WHERE $this->t_kullanici_mail = ?");
								$guncelle->execute(array(0,$kuki_id));
								$guncelle = $guncelle->fetchAll();
								header("refresh:3;url=kayit_ol.php");
								return "Kayıt Başarılı.Anasayfaya yönlendiriliyorsunuz.";		
							}
							else
							{
								return "Güncellemede hata";
							}
						}
						else
						{	
							$sec = $db->prepare("SELECT hata_no FROM uyeler WHERE $this->t_kullanici_mail = ?");
							$sec ->execute(array($kuki_id));
							$sec =$sec->fetchAll();
							foreach ($sec as $key) 
							{
								if ($key['hata_no']>1) 
								{	
									$sil = "DELETE * FROM $this->t_hata_no WHERE $this->t_kullanici_mail =$kuki_id";
									$db->exec($sil);
									header("refresh:3;url=kayit_ol.php");
									return "3 Kez Yanlış girdiniz.Anasayfaya yönlendiriliyorsunuz.";					
								}
								else
								{
									$guncelle =$db->prepare("UPDATE $this->tablo_ad SET $this->t_hata_no = ? WHERE $this->t_kullanici_mail = ?");
									$key['hata_no']++;
									$guncelle->execute(array($key['hata_no'],$kuki_id));
									$guncelle = $guncelle->fetchAll();
									return "Tekrar Deneyiniz.".$key['hata_no'].". hatanız.";
								}
							}
						}
					}
				}
			}
		}


	}

 ?>
