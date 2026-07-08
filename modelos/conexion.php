<?php

class Conexion{

	static public function conectar(){

		$link = new PDO("mysql:host=localhost;dbname=qwerty_fox_control",
			            "root",
			            "");

		$link->exec("set names utf8");

		return $link;

	}

	static public function conectar1(){
		$conectado = @fsockopen("www.google.com", 80); 
		if ($conectado) {
			fclose($conectado);
			$link = new PDO("mysql:host=srv1289.hstgr.io;dbname=u845379873_fox",
			            "u845379873_fox",
			            "5|gFJsbzAy$");

			$link->exec("set names utf8");

			return $link;
		} else {
			echo ("No tienes internet");
		}
		
	}

}