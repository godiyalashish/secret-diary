<?php 

   session_start();

   if(array_key_exists(content, $_POST){

   	include("connection.php");

   	$query = "UPDATE `diaryusers` SET `diary` = '".msqli_real_escape_string($link,$_POST['content'])."' WHERE id = ".msqli_real_escape_string($link,$_SESSION['id'])." "LIMIT 1";

   	mysqli_query($link,$query);

?>