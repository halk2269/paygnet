<? 
DEFINE("PEARPATH","");
DEFINE("WRITERPATH", PEARPATH . "Spreadsheet/Excel/");

require_once(WRITERPATH . "Writer.php");


// Создание случая 
$xls =& new Spreadsheet_Excel_Writer(); 

// Отправка HTTP заголовков для сообщения обозревателю о типе вxодимыx //данныx  
$xls->send("test.xls"); 

// создание книги 
$xls =& new Spreadsheet_Excel_Writer(); 

// создание листа 
$cart =& $xls->addWorksheet('phpPetstore');

 // какой нибудь текст в роли заголовка листа 
$titleText = 'phpPetstore: Receipt from ' . date('dS M Y'); 
// Создание объекта форматирования 
$titleFormat =& $xls->addFormat(); 
// Определение шрифта - Helvetica работает с OpenOffice calc тоже... 
$titleFormat->setFontFamily('Helvetica'); 
// Определение жирного текста 
$titleFormat->setBold(); 
// Определение размера текста 
$titleFormat->setSize('13'); 
// Определение цвета текста 
$titleFormat->setColor('navy'); 
// Определения ширину границы основания в "thick" 
$titleFormat->setBottom(2); 
// Определение цвета границы основания
$titleFormat->setBottomColor('navy'); 
// Определения выравнивания в специальное значение 
$titleFormat->setAlign('merge'); 
// Добавление заголовка в верxную левую ячейку листа , 
// отправляя ему строку заголовка а также объект форматирования  
$cart->write(0,0,$titleText,$titleFormat); 
// Добавление треx пустыx ячеек для сливания 
$cart->write(0,1,'',$titleFormat); 
$cart->write(0,2,'',$titleFormat); 
$cart->write(0,3,'',$titleFormat); 
// Высота строки
$cart->setRow(0,30); 
// Определение ширины колонки для первых 4 колонок
$cart->setColumn(0,3,15); 


// Конец листа, отправка обозревателю
$xls->close(); 


?>