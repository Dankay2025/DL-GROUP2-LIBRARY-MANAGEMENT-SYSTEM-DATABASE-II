<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';
post_only();if(!verify_csrf_token($_POST['csrf_token']??null)){set_flash('error','Session expired.');redirect('/books/index.php');}$id=(int)($_POST['id']??0);try{$st=$pdo->prepare('DELETE FROM books WHERE book_id=?');$st->execute([$id]);set_flash('success','Book deleted.');}catch(PDOException $e){set_flash('error','This book cannot be deleted because it has loan history or linked records.');}redirect('/books/index.php');