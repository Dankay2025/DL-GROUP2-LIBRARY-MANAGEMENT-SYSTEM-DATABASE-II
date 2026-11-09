<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
post_only();if(!verify_csrf_token($_POST['csrf_token']??null)){set_flash('error','Session expired.');redirect('/authors/index.php');}try{$s=$pdo->prepare('DELETE FROM authors WHERE author_id=?');$s->execute([(int)($_POST['id']??0)]);set_flash('success','Record deleted.');}catch(PDOException $e){set_flash('error','This record cannot be deleted because it is being used by another record.');}redirect('/authors/index.php');