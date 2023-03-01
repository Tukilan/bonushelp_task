<?
	/**
	 * 
	 */
	class person
	{
		
		// Берет весь список из person и добавляет в очередь 
		static function PreparePersonMail(){
			$mail_tmp_name = '[[NAME]] Для вас подарок!';
			$mail_tmp_text = 'Добрый день [[NAME]], благодарим за использование нашего продукта  и в благодарность мы предоставляем вам на выбор несколько подарков.';
			$q = 'SELECT * FROM `person`';
			$person = \DB::exec($q,['fetch'=>1]);
			$query = '';
			$flag = 1; // Флаг для  multi_query
			foreach ($person as $k => $v) {
				$mail_name = str_replace('[[NAME]]',$v['person_name'],$mail_tmp_name);
				$mail_text = str_replace('[[NAME]]',$v['person_name'],$mail_tmp_text);
				$query .= 'INSERT INTO `mailing`(`mailing_person`,`mailing_status`,`mailing_name`,`mailing_text`) VALUES("'.$v['person_id'].'",0,"'.$mail_name.'","'.$mail_text.'");';
				$flag++;
				if ($flag == 1000){
					\DB::exec($query,['multi'=>1]);
					$query = '';
					$flag = 0;
				}
			}
			if ($query) \DB::exec($query,['multi'=>1]);
			echo json_encode(['error'=>0,'msg'=>'Успешно добавлено'],JSON_UNESCAPED_UNICODE);
		}


		static function FakeMail(){
			// Берем всю очередь рассылки, где статус = 0 (Не отправлен), тем самым берем тех, кому не отправили.
			// По рандому проставляем статус на 1 для имитации ошибочной отправки или же внезапной остановки выполнения рассылки (К примеру перезагрузили сервер)
		 	$q = 'SELECT * FROM `mailing` WHERE `mailing_status` = 0 ';
		 	$res = \DB::exec($q,['fetch'=>1]);
		 	foreach ($res as $k => $v) {
		 		$rand = rand(0,1);
		 		$q = 'UPDATE `mailing` SET `mailing_status` = '.$rand.' WHERE `mailing_id` = '.$v['mailing_id'];
		 		var_dump($q);
		 		\DB::exec($q);
		 	}
		}
	}
?>