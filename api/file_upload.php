<?
	class file_upload
	{	
		static function file_upload_person(){
			global $server;
			$res = new \stdClass();
	        $res->error = 1;
	        $res->msg = 'Отсутствуют файлы';
			if (isset($_FILES)){
				$data_files = $_FILES['file'];
				// Доступные форматы для загрузки
				$ALLOW_EXT_UPLOAD = array('csv');
        		$uplohost = $server['upload_dir'];
        		if (count($files = $data_files['name'])) {
        			foreach ($files as $k => $v) {
        				$part = explode(".", $v);
        				$ext = strtolower($part[count($part) - 1]);
        				if (!in_array($ext,$ALLOW_EXT_UPLOAD)){
		                    $res->error = 1;
		                    $res->msg = 'Файл имеет недопустимый формат. Разрешённые форматы файлов для загрузки: <b>.'.implode('</b>, <b>.',$ALLOW_EXT_UPLOAD).'</b>';
		                    break;
		                }
		                // Делаю файлу уникальное имя для исключении коллизии
		                $name = time() . '_' . ($k+1);
		                $hashFile = md5($name);
		                $fname = $hashFile. "." . $ext;
		                $file = $uplohost . $fname;

		                if (!file_exists($uplohost)) {
		                    mkdir($uplohost . $uploaddir, 0755, true);
		                }

		                // перемещаем файл
		                if (move_uploaded_file($data_files['tmp_name'][$k], $file)) {
		                	$dataCsv = array_map('str_getcsv', file($file)); 
		                    $query = '';
		                    $flag = 0;
		                    // Через multi_query посылаю в базу по 1000 запросов 
		                    foreach ($dataCsv as $value) {
		                    	$query .= 'INSERT INTO `person`(`person_number`,`person_name`) VALUES("'.$value[0].'","'.$value[1].'");';
		                    	$flag++;
		                    	if ($flag == 1000){
		                    		\DB::exec($query,['multi'=>1]);
		                    		$query = '';
		                    		$flag = 0;
		                    	}
		                    }
		                    // После обработки массива могут остаться поля которые не добавились из за ограничения в 1000 строк.
		                    if ($query) \DB::exec($query,['multi'=>1]);
		                    $res->error = 0;
		                    $res->msg = 'Успешно';
		                } else {
		                    $res->error = 1;
		                    $res->msg = 'Произошла ошибка при загрузки файла';
		                    break;
		                }
        			}
        		}
			}
			echo json_encode($res,JSON_UNESCAPED_UNICODE);
		}
	}
?>