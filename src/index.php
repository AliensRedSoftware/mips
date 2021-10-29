<?php
use std, gui, framework;

$prefix	=	'-';//Префикс
$cmd	=	str::split(System::getProperties()['sun.java.command'], " ");
array_shift($cmd);
if (!$cmd) {
	GUI();//-->Запуск в гип
} else {
	foreach ($cmd as $command) {
		if($command == $prefix . 'п'){
			getHelp();
		}elseif ($command == $prefix . 'гип'){
			GUI();
		}elseif($command == $prefix . 'install'){
			$command = str::split($cmd[1], '=');
			if($command[0] == $prefix . 'скин'){
				$exec = $command[1]; //jlil файл
				if(fs::isFile($exec)){
					$framework = $command[2];
					foreach(getFrameWork() as $fwk){
						if($fwk == $framework) {
							$selectedSkin = $command[3];
							foreach(getSkins($fwk) as $skin){
								if($selectedSkin == $skin){
									installSkin($exec, $framework, $skin, function ($e){
										if($e){
											echo "Скин успешно установлен! :)\n";
										}else{
											echo "Скин не был установлен так как он уже установлен...\n";
										}
									});
									return;
								}
							}
							echo "[$cmd[0]] => Ошибка установка скина :(
		скин не был найден для jlil!
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
							return;
						}
					}
					echo "[$cmd[0]] => Ошибка установка скина :(
		Фреймворки не был найден для jlil!
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
					return;
				} else {
					echo "[$cmd[0]] => Ошибка установка скина :(
		Не найден jlil!
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
					return;
				}
			}
			echo "[$cmd[0]] => Ошибка установка скина :(
		Не найден ключ установки скина
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
			return;
		}elseif($command == $prefix . 'uninstall'){
			$command = str::split($cmd[1], '=');
			if($command[0] == $prefix . 'скин'){
				$exec = $command[1]; //jlil файл
				if(fs::isFile($exec)){
					$framework = $command[2];
					foreach(getFrameWork() as $fwk){
						if($fwk == $framework) {
							$selectedSkin = $command[3];
							foreach(getSkins($fwk) as $skin){
								if($selectedSkin == $skin){
									deleteSkin($exec, $framework, $skin, function ($e){
										if($e){
											echo "Скин успешно удален! :)\n";
										}else{
											echo "Скин не был удален так как он уже удален...\n";
										}
										app()->shutdown();
									});
									return;
								}
							}
							echo "[$cmd[0]] => Ошибка удаление скина :(
		скин не был найден для jlil!
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
							return;
						}
					}
					echo "[$cmd[0]] => Ошибка удаление скина :(
		Фреймворки не был найден для jlil!
		Пример команды: -install -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
					return;
				} else {
					echo "[$cmd[0]] => Ошибка удаление скина :(
		Не найден jlil!
		Пример команды: -uninstall -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
					return;
				}
			}
			echo "[$cmd[0]] => Ошибка удаление скина :(
		Не найден ключ удаление скина
		Пример команды: -uninstall -скин=jlil.1.0.8.jar=awt=bootstrap2\n";
			return;
		}else {
			getHelp();
			return;
		}
	}
}

	/**
	 * Запуск в гип
	 */
	function GUI () {
		UXApplication::runLater(function () {
			$form				=	new UXForm();
			$form->title		=	'Мипс 1.0.1 :)';
			$form->size			=	[320, 240];
			$form->resizable	=	false;
			$framework	=	new UXCombobox();
			$framework->position	=	[8, 8];
			$framework->width		=	312;
			foreach(getFrameWork() as $val) {
				$framework->items->add($val);
			}
			$framework->selectedIndex = 0;
			$skin			=	new UXCombobox();
			$skin->position	=	[8, 40];
			$skin->width	=	312;
			$framework->on('action', function () use ($skin, $framework) {
				$skin->items->clear();
				foreach(getSkins($framework->selected) as $val) {
					$skin->items->add($val);
				}
				$skin->selectedIndex = 0;
			});
			foreach(getSkins($framework->selected) as $val) {
				$skin->items->add($val);
			}
			$skin->selectedIndex = 0;
			$install	=	new UXButton('Установить');
			$install->position = [232, 72];
			$install->on('action', function () use ($skin , $framework) {
				if(uiconfirm('Вы точно желаете установить скин ?')){
					$skin=$skin->selected;
					$framework=$framework->selected;
					installSkin('jlil.jar', $framework, $skin, function ($e) {
						if($e){
							UXDialog::show('Скин успешно установлен! :)');
						}else{
							UXDialog::show('Скин не был установлен так как он уже установлен...');
						}
					});
				}
			});
			$uninstall	=	new UXButton('Удалить');
			$uninstall->position = [152, 72];
			$uninstall->on('action', function () use ($skin , $framework) {
				if(uiconfirm('Вы точно желаете удалить скин ?')){
					$skin=$skin->selected;
					$framework=$framework->selected;
					deleteSkin('jlil.jar', $framework, $skin, function ($e) {
						if($e){
							UXDialog::show('Скин успешно удален! :)');
						}else{
							UXDialog::show('Скин не был удален так как он уже удален...');
						}
					});
				}
			});
			$form->add($framework);
			$form->add($skin);
			$form->add($install);
			$form->add($uninstall);
			$form->show();
		});
	}
	
	/**
	 * Установить скин
	 * path - Наш путь
	 * framework - ФреймВорк
	 * selectedSkin - Скин от фреймворка
	 */
	function installSkin($path, $framework, $selectedSkin, $callback){
		$jar = new \\\bundle\zip\ZipFileScript();
		$jar->path = $path;
		$jar->on('packAll', function () use ($jar, $selectedSkin, $callback) {
			fs::clean('.tmp');
			fs::delete('.tmp');
			$ini = new IniStorage();
			$ini->path = 'config.ini';
			$ini->set('selected', $selectedSkin, 'skin');
			/*
			$os = System::getProperty('os.name');
		    if ($os == 'Linux') {
		        execute('./' . $jar->path);
		    } else {
		        execute('java -jar jstar.exe');
		    }*/
			if(is_callable($callback)) {
				$callback(true);
			}
			app()->shutdown();
		});
		if (!$jar->has('.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator() . $selectedSkin . '.fx.css')) {
			$skins = fs::scan('.' . fs::separator() . 'skins' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator());
			fs::makeDir('.tmp');
			$jar->unpackAsync('.' . fs::separator() . '.tmp', null, function () {
			});
			$jar->on('unpackAll', function () use ($framework, $selectedSkin, $skins, $jar) {
				//-->Подготовка директорий
				fs::makeDir('.tmp' . fs::separator() . '.theme');
				fs::makeDir('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework);
				fs::makeDir('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin);
				//-->Упаковать
				foreach($skins as $file){
					$skn = str::split($file, fs::separator());
					$skin = $skn[count($skn) - 1];
					fs::copy($file, '.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator() . $skin);
					echo "-->$file\n";
				}
				echo "[PACK] => jlil\n";
				$jar->addDirectoryAsync('.tmp', -1, function ($file){
					echo "-->$file\n";
				});
			});

		} else {
			if(is_callable($callback)) {
				$callback(false);
			}
		}
	 }
	 
	/**
	 * Удалитть скинь
	 * path - Наш путь
	 * framework - ФреймВорк
	 * selectedSkin - Скин от фреймворка
	 */
	function deleteSkin($path, $framework, $selectedSkin, $callback){
		$jar = new \\\bundle\zip\ZipFileScript();
		$jar->path = $path;
		$jar->on('packAll', function () use ($jar, $selectedSkin, $callback) {
			fs::clean('.tmp');
			fs::delete('.tmp');
			$ini = new IniStorage();
			$ini->path = 'config.ini';
			$ini->set('selected', $selectedSkin, 'skin');
			/*
			$os = System::getProperty('os.name');
		    if ($os == 'Linux') {
		        execute('./' . $jar->path);
		    } else {
		        execute('java -jar jstar.exe');
		    }*/
			if(is_callable($callback)) {
				$callback(true);
			}
			app()->shutdown();
		});
		if($jar->has('.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator() . $selectedSkin . '.fx.css')) {
			$skins = fs::scan('.' . fs::separator() . 'skins' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator());
			fs::makeDir('.tmp');
			$jar->unpackAsync('.' . fs::separator() . '.tmp', null, function () {
			});
			$jar->on('unpackAll', function () use ($framework, $selectedSkin, $skins, $jar) {
				//-->Подготовка директорий
				fs::clean('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin);
				fs::delete('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin);
				//-->Упаковать
				echo "[PACK] => jlil\n";
				$jar->addDirectoryAsync('.tmp', -1, function ($file){
					echo "-->$file\n";
				});
			});

		} else {
			if(is_callable($callback)) {
				$callback(false);
			}
		}
	}
	/**
	 * Возвращаем помощь
	 * @return string
	 */
	 function getHelp () {
		echo "
Добро пожаловать в программу мипс 1.0.1 :)
	Описание программы:Установка скинов для jlil
		Общее команды (4):
			-п	=>	О программе и вывод всех команд
			-гип	=>	Запуск программы в гип
			-скин	=>	Выбранный скин
			-install	=>	Установить
			-uninstall	=>	Удалить
		Чтобы запустить программу в гип запустите программу без ключей
			Пример (0): \"./mips\" <= без ключей
			Пример (1): \"./mips -п\" <= запуск с ключами\n";
	 }
	/**
	 * Возвращаем скины
	 * ----------------
	 * @return array
	 */
	function getSkins($framework) {
		$arr = [];
		$files = fs::scan('.' . fs::separator() . 'skins' . fs::separator() . $framework . fs::separator(), ['excludeFiles' => true]);
		foreach ($files as $file) {
			$skins = str::split($file, fs::separator());
			array_push($arr, $skins[count($skins) - 1]);
		}
		return $arr;
	}
	
	/**
	 * Возвращаем фреймворки
	 * ---------------------
	 * @return array
	 */
	function getFrameWork() {
		$arr = [];
		$files = fs::scan('.' . fs::separator() . 'skins', ['excludeFiles' => true]);
		foreach ($files as $file) {
			$skins = str::split($file, fs::separator());
			foreach($arr as $ls) {
				if ($ls == $skins[2]) {
					$created = false;
					break;
				} else {
					$created = true;
				}
			}
			if ($created) {
				array_push($arr, $skins[2]);
			}
			if (!$arr) {
				array_push($arr, $skins[2]);
			}
		}
		return $arr;
	}

