<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
post_only();if(!verify_csrf_token($_POST['csrf_token']??null)){set_flash('error','Session expired.');redirect('/members/index.php');}try{$s=$pdo->prepare('DELETE FROM members WHERE member_id=?');$s->execute([(int)($_POST['id']??0)]);set_flash('success','Member deleted.');}catch(PDOException $e){set_flash('error','This member cannot be deleted because loan history exists.');}redirect('/members/index.php');