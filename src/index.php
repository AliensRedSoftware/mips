<?php
use std, gui, framework;

$prefix	=	'-';//Префикс
$cmd	=	str::split(System::getProperties()['sun.java.command'], " ");
array_shift($cmd);
if (!$cmd) {
	GUI();//-->Запуск в гип
} else {
	foreach ($cmd as $command) {
		$command = str::split($command, '=');
		if ($command[0] == $prefix . 'п') {
			getHelp();
		} elseif ($command[0] == $prefix . 'гип') {
			GUI();
		} elseif ($command[0] == $prefix . 'скин') {
			$exec = $command[1]; //jlil файл
			if (fs::isFile($exec)) {
				$framework = $command[2];
				foreach (getFrameWork() as $fwk) {
					if ($fwk == $framework) {
						$selectedSkin = $command[3];
						foreach (getSkins($fwk) as $skin) {
							if ($selectedSkin == $skin) {
								installSkin($exec, $framework, $skin);
								echo "Скин успешно установлен! :)\n";
								
							}
						}
						echo "[$command[0]] => Ошибка установка скина :(\n
						Скин не был найден для jlil!\n
						Пример команды: -скин=jlil.1.0.8.jar=awt=bootstrap2";
						return;
					}
				}
				echo "[$command[0]] => Ошибка установка скина :(\n
				Фреймворки не был найден для jlil!\n
				Пример команды: -скин=jlil.1.0.8.jar=awt=bootstrap2";
			} else {
				echo "[$command[0]] => Ошибка установка скина :(\n
				Не найден jlil!\n
				Пример команды: -скин=jlil.1.0.8.jar=awt=bootstrap2";
			}
		} else {
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
			$form->title		=	'Мипс 1.0.0 :)';
			$form->size			=	[320, 240];
			$form->resizable	=	false;
			$form->on('showing', function () use ($form) {
				$framework		=	new UXCombobox();
				$framework->position	=	[8, 8];
				$framework->width		=	312;
				$form->add($framework);
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
				});
				foreach(getSkins($framework->selected) as $val) {
					$skin->items->add($val);
				}
				$form->add($skin);
				$install	=	new UXButton('Установить');
				$install->position = [240, 72];
				$form->add($install);
			});
			$form->show();
		});
	}
	
	/**
	 * Установить скин
	 */
	function installSkin ($path, $framework, $selectedSkin) {
		$jar = new \\\bundle\zip\ZipFileScript();
		$jar->path = $path;
		$jar->on('packAll', function () use ($jar, $selectedSkin) {
			fs::clean('.tmp');
			fs::delete('.tmp');
			$ini = new IniStorage();
			$ini->path = 'config.ini';
			$ini->set('selected', $selectedSkin, 'skin');
			execute('java -jar ' . $jar->path);
			app()->shutdown();
		});
		if (!$jar->has('.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator() . $selectedSkin . '.fx.css')) {
			$skins = fs::scan('.' . fs::separator() . "skins" . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator());
			fs::makeDir('.tmp');
			$jar->unpackAsync('.' . fs::separator() . '.tmp', null, function () {
			});
			$jar->on('unpackAll', function () use ($framework, $selectedSkin, $skins, $jar) {
				//-->Подготовка директорий
				fs::makeDir('.tmp' . fs::separator() . '.theme');
				fs::makeDir('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework);
				fs::makeDir('.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin);
				//-->Упаковать
				foreach ($skins as $file) {
					$skn = str::split($file, fs::separator());
					$skin = $skn[count($skn) - 1];
					fs::copy($file, '.tmp' . fs::separator() . '.theme' . fs::separator() . $framework . fs::separator() . $selectedSkin . fs::separator() . $skin);
				}
				$jar->addDirectoryAsync('.tmp', -1, function (){
					
				});
			});
			return true;
		} else {
			return false;
		}
	 }
	 

	/**
	 * Возвращаем помощь
	 */
	 function getHelp () {
		echo "Добро пожаловать в программу мипс 1.0.0 :)\n
		Описание программы:Установка скинов для jlil\n
		Общее команды (2):
			-п		=>	О программе и вывод всех команд\n
			-гип	=>	Запуск программы в гип\n
			-скин	=>	Выбранный скин
		Чтобы запустить программу в гип запустите программу без ключей\n
		Пример (0): \"java -jar mips.jar\" <= без ключей \n
		Пример (1): \"java -jar mips.jar -п\" <= запуск с ключами";
	 }
	/**
	 * Возвращаем скины
	 * ----------------
	 * @return Array
	 */
	function getSkins($framework) {
		$arr = [];
		$files = fs::scan('.' . fs::separator() . "skins" . fs::separator() . $framework . fs::separator(), ['excludeFiles' => true]);
		foreach ($files as $file) {
			$skins = str::split($file, fs::separator());
			array_push($arr, $skins[count($skins) - 1]);
		}
		return $arr;
	}
	
	/**
	 * Возвращаем фреймворки
	 * ---------------------
	 * @return Array
	 */
	function getFrameWork() {
		$arr = [];
		$files = fs::scan('.' . fs::separator() . "skins", ['excludeFiles' => true]);
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

