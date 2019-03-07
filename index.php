<?php
	define('TABLE', 'reviews');
	$dbh = new \PDO('mysql:host=127.0.0.1;dbname=for_test', 'root', '');

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

		parse_str($_POST['data'],$data);

		foreach ($data as $key => $value) {
			$value = trim($value)?$value:null;
			$columns[]= $key;
			$values[':'.$key]= $value;
		}

		$columns[] = 'date';
		$values[':date'] = time();

		$sql = 'INSERT INTO ' . TABLE . '
		(' . implode(',', $columns) . ')
		VALUES
		(' . implode(',', array_keys($values)) . ')
			';

		$sth = $dbh->prepare($sql);
		$res = $sth->execute($values);
		if (!$res) {
			header('HTTP/1.1 500 Internal Server Error');
			throw new Exception($sth->errorInfo()[2], 1);
			
		}
		exit(0);
	}
	$sql = 'SELECT * FROM ' . TABLE;
	$sth = $dbh->prepare($sql);
	$res = $sth->execute();

	$reviews = [];
	if (false !==$res) { 
		$reviews  = $sth->fetchAll(\PDO::FETCH_CLASS);
	}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Отзывы</title>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
</head>
<body>
	<h1>Форма отзывов</h1>
	<form class="form_review validate" method="POST"> 
		<div>
			<label>Имя</label>
			<input type="text" name="name">		
		</div>
		<div>
			<label>Email</label>
			<input type="email" name="email">	
		</div>
		<div>
			<label>Текст</label>
			<textarea name="text"></textarea> 
		</div>
		<input type="submit" name="Отправить">	
   </form>
   <h2>Отзывы</h2>
   <?php foreach ($reviews as $key => $review) { ?>
		<div>
			Имя: <?=$review->name?>,
			Email: <?=$review->email?>, 
			Текст: <?=$review->text?>,
			Дата: <?=date('d.m.Y H:i:s', $review->date)?>
		</div>
	<?php } ?>
</body>

</html>
<script> 
	$(".validate").validate({
	  	rules: {
	    	name: "required",
	    	email: {
		      	required: true,
		     	email: true
    		},
    		text: "required"
	  },
	  messages: {
	    	name: "Введите имя",
	    	email: {
	      		required: "Введите email",
	      		email: "Ваш email должен быть формата name@domain.com"
	    	},
	    	text: {
	      		required: "Введите текст отзыва",
	    	}
	  	}
	});

	$('.form_review').submit(function(){
		if ($(this).find('input.error').length) return false;
		var data = $(this).serialize();
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                data:data,
            },
            success: function success(data) {
                alert("Коментарий был отправлен.");
            },
            error: function error(data) {
            	alert("Ошибка отправки");
            }

        });
		return false;
	});



</script>

<style type="text/css">
	div{
		margin: 20px;
		width: 150px;
	}
</style>