/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: data.js
 * 	Scripts for the fck_universalkey.html page.
 * 	Definition des 104 caracteres en hexa unicode.
 * 
 * File Authors:
 * 		Michel Staelens (michel.staelens@wanadoo.fr)
 * 		Abdul-Aziz Al-Oraij (top7up@hotmail.com)
 */

var Maj = new Array() ;
var Min = new Array() ;

Maj["Arabic"]				="0651|0021|0040|0023|0024|0025|005E|0026|002A|0029|0028|005F|002B|064E|064B|064F|064C|0625|0625|2018|00F7|00D7|061B|003C|003E|0650|064D|005D|005B|0623|0623|0640|060C|002F|003A|0022|007E|0652|007D|007B|0622|0622|2019|002C|002E|061F|007C|0020|0020|0020|0020|0020" ;
Min["Arabic"]				="0630|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0636|0635|062B|0642|0641|063A|0639|0647|062E|062D|062C|062F|0634|0633|064A|0628|0644|0627|062A|0646|0645|0643|0637|0626|0621|0624|0631|0644|0627|0649|0629|0648|0632|0638|005C|0020|0020|0020|0020" ;
Maj["Belarusian (C)"]		="0401|0021|0022|2116|003B|0025|003A|003F|002A|0028|0029|005F|002B|0419|0426|0423|041A|0415|041D|0413|0428|040E|0417|0425|0027|0424|042B|0412|0410|041F|0420|041E|041B|0414|0416|042D|042F|0427|0421|041C|0406|0422|042C|0411|042E|002C|0020|0020|0020|0020|0020|0020" ;
Min["Belarusian (C)"]		="0451|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0439|0446|0443|043A|0435|043D|0433|0448|045E|0437|0445|0027|0444|044B|0432|0430|043F|0440|043E|043B|0434|0436|044D|044F|0447|0441|043C|0456|0442|044C|0431|044E|002E|0020|0020|0020|0020|0020|0020" ;
Maj["Bulgarian (C)"]		="007E|0021|003F|002B|0022|0025|003D|003A|002F|005F|2116|0406|0056|044B|0423|0415|0418|0428|0429|041A|0421|0414|0417|0426|00A7|042C|042F|0410|041E|0416|0413|0422|041D|0412|041C|0427|042E|0419|042A|042D|0424|0425|041F|0420|041B|0411|0029|0020|0020|0020|0020|0020" ;
Min["Bulgarian (C)"]		="0060|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|002E|002C|0443|0435|0438|0448|0449|043A|0441|0434|0437|0446|003B|044C|044F|0430|043E|0436|0433|0442|043D|0432|043C|0447|044E|0439|044A|044D|0444|0445|043F|0440|043B|0431|0028|0020|0020|0020|0020|0020" ;
Maj["Croatian (L)"]			="00B8|0021|0022|0023|0024|0025|0026|002F|0028|0029|003D|003F|00A8|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|0160|0110|0041|0053|0044|0046|0047|0048|004A|004B|004C|010C|0106|0059|0058|0043|0056|0042|004E|004D|017D|003B|003A|003C|003E|005F|002D|002A|002B" ;
Min["Croatian (L)"]			="00B8|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0027|00A8|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|0161|0111|0061|0073|0064|0066|0067|0068|006A|006B|006C|010D|0107|0079|0078|0063|0076|0062|006E|006D|017E|002C|002E|003C|003E|005F|002D|002A|002B" ;
Maj["Czech (L)"]			="00B0|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0025|02C7|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|002F|0028|0041|0053|0044|0046|0047|0048|004A|004B|004C|0022|0027|0059|0058|0043|0056|0042|004E|004D|003F|003A|005F|005B|007B|0021|0020|0148|010F" ;
Min["Czech (L)"]			="003B|002B|011B|0161|010D|0159|017E|00FD|00E1|00ED|00E9|003D|00B4|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|00FA|0029|0061|0073|0064|0066|0067|0068|006A|006B|006C|016F|00A7|0079|0078|0063|0076|0062|006E|006D|002C|002E|002D|005D|007D|00A8|0040|00F3|0165" ;
Maj["Danish (L)"]			="00A7|0021|0022|0023|00A4|0025|0026|002F|0028|0029|003D|003F|0060|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|00C5|005E|0041|0053|0044|0046|0047|0048|004A|004B|004C|00C6|00D8|003E|005A|0058|0043|0056|0042|004E|004D|003B|003A|002A|005F|007B|007D|005C|007E" ;
Min["Danish (L)"]			="00BD|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002B|00B4|0071|0077|0065|0072|0074|0079|0075|0069|006F|0070|00E5|00A8|0061|0073|0064|0066|0067|0068|006A|006B|006C|00E6|00F8|003C|007A|0078|0063|0076|0062|006E|006D|002C|002E|0027|002D|005B|005D|007C|0040" ;
Maj["Farsi"]				="0020|0021|0040|0023|0024|0025|005E|0026|002A|0029|0028|005F|002B|0020|0020|0020|0020|0020|0020|0020|00F7|00D7|0020|007D|007B|0020|0020|005D|005B|0623|0622|0640|060C|061B|003A|0022|007E|0020|0020|0020|0020|0020|2019|003E|003C|061F|007C|0020|0020|0020|0020|0020"
Min["Farsi"]				="067E|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0636|0635|062B|0642|0641|063A|0639|0647|062E|062D|062C|0686|0634|0633|064A|0628|0644|0627|062A|0646|0645|0643|06AF|0638|0637|0632|0631|0630|062F|0621|0648|002E|002F|005C|0020|0020|0020|0020|0020"
Maj["Finnish (L)"]			="00A7|0021|0022|0023|00A4|0025|0026|002F|0028|0029|003D|003F|0060|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|00C5|005E|0041|0053|0044|0046|0047|0048|004A|004B|004C|00D6|00C4|003E|005A|0058|0043|0056|0042|004E|004D|003B|003A|002A|005F|007B|007D|005C|007E" ;
Min["Finnish (L)"]			="00BD|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002B|00B4|0071|0077|0065|0072|0074|0079|0075|0069|006F|0070|00E5|00A8|0061|0073|0064|0066|0067|0068|006A|006B|006C|00F6|00E4|003C|007A|0078|0063|0076|0062|006E|006D|002C|002E|0027|002D|005B|005D|007C|0040" ;
Maj["French (L)"]			="0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|00B0|002B|0023|0041|005A|0045|0052|0054|0059|0055|0049|004F|0050|00A8|0025|0051|0053|0044|0046|0047|0048|004A|004B|004C|004D|00B5|0057|0058|0043|0056|0042|004E|003F|002E|002F|00A7|003C|005B|007B|00A3|007E|0020" ;
Min["French (L)"]			="0026|00E9|0022|0027|0028|002D|00E8|005F|00E7|00E0|0029|003D|0040|0061|007A|0065|0072|0074|0079|0075|0069|006F|0070|005E|00F9|0071|0073|0064|0066|0067|0068|006A|006B|006C|006D|002A|0077|0078|0063|0076|0062|006E|002C|003B|003A|0021|003E|005D|007D|0024|007E|0020" ;
Maj["Greek"]				="007E|0021|0040|0023|0024|0025|0390|0026|03B0|0028|0029|005F|002B|003A|03A3|0395|03A1|03A4|03A5|0398|0399|039F|03A0|0386|038F|0391|03A3|0394|03A6|0393|0397|039E|039A|039B|038C|0022|0396|03A7|03A8|03A9|0392|039D|039C|003C|003E|003F|0388|0389|038A|03AA|03AB|038E" ;
Min["Greek"]				="0060|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|003B|03C2|03B5|03C1|03C4|03C5|03B8|03B9|03BF|03C0|03AC|03CE|03B1|03C3|03B4|03C6|03B3|03B7|03BE|03BA|03BB|03CC|0027|03B6|03C7|03C8|03C9|03B2|03BD|03BC|002C|002E|002F|03AD|03AE|03AF|03CA|03CB|03CD" ;
Maj["Hebrew"]				="007E|0021|0040|0023|0024|0025|005E|0026|002A|0028|0029|005F|002B|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|007B|007D|0041|0053|0044|0046|0047|0048|004A|004B|004C|003A|0022|005A|0058|0043|0056|0042|004E|004D|003C|003E|003F|0020|0020|0020|0020|0020|0020" ;
Min["Hebrew"]				="0060|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|002F|0027|05E7|05E8|05D0|05D8|05D5|05DF|05DD|05E4|005B|005D|05E9|05D3|05D2|05DB|05E2|05D9|05D7|05DC|05DA|05E3|002C|05D6|05E1|05D1|05D4|05E0|05DE|05E6|05EA|05E5|002E|0020|0020|0020|0020|0020|0020" ;
Maj["Hungarian (L)"]		="00A7|0027|0022|002B|0021|0025|002F|003D|0028|0029|00ED|00DC|00D3|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|0150|00DA|0041|0053|0044|0046|0047|0048|004A|004B|004C|00C9|00C1|0170|00CD|0059|0058|0043|0056|0042|004E|004D|003F|002E|003A|002D|005F|007B|007D" ;
Min["Hungarian (L)"]		="0030|0031|0032|0033|0034|0035|0036|0037|0038|0039|00F6|00FC|00F3|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|0151|00FA|0061|0073|0064|0066|0067|0068|006A|006B|006C|00E9|00E1|0171|00ED|0079|0078|0063|0076|0062|006E|006D|002C|002E|003A|002D|005F|007B|007D" ;
Maj["Diacritical (L)"]		="0060|00B4|005E|00A8|007E|00B0|00B7|00B8|00AF|02D9|02DB|02C7|02D8|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|00C6|02DD|0041|0053|0044|0046|0047|0048|004A|004B|004C|0141|0152|0059|0058|0043|0056|0042|004E|004D|01A0|01AF|00D8|0126|0110|0132|00DE|00D0|00DF" ;
Min["Diacritical (L)"]		="0060|00B4|005E|00A8|007E|00B0|00B7|00B8|00AF|02D9|02DB|02C7|02D8|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|00E6|02DD|0061|0073|0064|0066|0067|0068|006A|006B|006C|0142|0153|0079|0078|0063|0076|0062|006E|006D|01A1|01B0|00F8|0127|0111|0133|00FE|00F0|00DF" ;
Maj["Macedonian (C)"]		="007E|0021|201E|201C|2019|0025|2018|0026|002A|0028|0029|005F|002B|0409|040A|0415|0420|0422|0405|0423|0418|041E|041F|0428|0403|0410|0421|0414|0424|0413|0425|0408|041A|041B|0427|040C|0401|0417|040F|0426|0412|0411|041D|041C|0416|003B|003A|003F|002A|005F|007B|007D" ;
Min["Macedonian (C)"]		="0060|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0459|045A|0435|0440|0442|0455|0443|0438|043E|043F|0448|0453|0430|0441|0434|0444|0433|0445|0458|043A|043B|0447|045C|0451|0437|045F|0446|0432|0431|043D|043C|0436|002C|002E|002F|0027|002D|005B|005D" ;
Maj["Norwegian (L)"]		="00A7|0021|0022|0023|00A4|0025|0026|002F|0028|0029|003D|003F|0060|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|00C5|005E|0041|0053|0044|0046|0047|0048|004A|004B|00D8|00C6|00C4|003E|005A|0058|0043|0056|0042|004E|004D|003B|003A|002A|005F|007B|007D|005C|007E" ;
Min["Norwegian (L)"]		="00BD|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002B|00B4|0071|0077|0065|0072|0074|0079|0075|0069|006F|0070|00E5|00A8|0061|0073|0064|0066|0067|0068|006A|006B|00F8|00E6|00E4|003C|007A|0078|0063|0076|0062|006E|006D|002C|002E|0027|002D|005B|005D|007C|0040" ;
Maj["Polish (L)"]			="002A|0021|0022|0023|00A4|0025|0026|002F|0028|0029|003D|003F|017A|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|0144|0107|0041|0053|0044|0046|0047|0048|004A|004B|004C|0141|0119|0059|0058|0043|0056|0042|004E|004D|003B|003A|005F|003C|005B|007B|02D9|00B4|02DB" ;
Min["Polish (L)"]			="0027|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002B|00F3|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|017C|015B|0061|0073|0064|0066|0067|0068|006A|006B|006C|0142|0105|0079|0078|0063|0076|0062|006E|006D|002C|002E|002D|003E|005D|007D|02D9|00B4|02DB" ;
Maj["Russian (C)"]			="0401|0021|0040|0023|2116|0025|005E|0026|002A|0028|0029|005F|002B|0419|0426|0423|041A|0415|041D|0413|0428|0429|0417|0425|042A|0424|042B|0412|0410|041F|0420|041E|041B|0414|0416|042D|042F|0427|0421|041C|0418|0422|042C|0411|042E|003E|002E|003A|0022|005B|005D|003F" ;
Min["Russian (C)"]			="0451|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0439|0446|0443|043A|0435|043D|0433|0448|0449|0437|0445|044A|0444|044B|0432|0430|043F|0440|043E|043B|0434|0436|044D|044F|0447|0441|043C|0438|0442|044C|0431|044E|003C|002C|003B|0027|007B|007D|002F" ;
Maj["Serbian (C)"]			="007E|0021|0022|0023|0024|0025|0026|002F|0028|0029|003D|003F|002A|0409|040A|0415|0420|0422|0417|0423|0418|041E|041F|0428|0402|0410|0421|0414|0424|0413|0425|0408|041A|041B|0427|040B|003E|0405|040F|0426|0412|0411|041D|041C|0416|003A|005F|002E|003A|0022|005B|005D" ;
Min["Serbian (C)"]			="0060|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0027|002B|0459|045A|0435|0440|0442|0437|0443|0438|043E|043F|0448|0452|0430|0441|0434|0444|0433|0445|0458|043A|043B|0447|045B|003C|0455|045F|0446|0432|0431|043D|043C|0436|002E|002D|002C|003B|0027|007B|007D" ;
Maj["Serbian (L)"]			="007E|0021|0022|0023|0024|0025|0026|002F|0028|0029|003D|003F|002A|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|0160|0110|0041|0053|0044|0046|0047|0048|004A|004B|004C|010C|0106|003E|0059|0058|0043|0056|0042|004E|004D|017D|003A|005F|002E|003A|0022|005B|005D" ;
Min["Serbian (L)"]			="201A|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0027|002B|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|0161|0111|0061|0073|0064|0066|0067|0068|006A|006B|006C|010D|0107|003C|0079|0078|0063|0076|0062|006E|006D|017E|002E|002D|002C|003B|0027|007B|007D" ;
Maj["Slovak (L)"]			="00B0|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0025|02C7|0051|0057|0045|0052|0054|005A|0055|0049|004F|0050|002F|0028|0041|0053|0044|0046|0047|0048|004A|004B|004C|0022|0021|0059|0058|0043|0056|0042|004E|004D|003F|003A|005F|003C|005B|010F|0029|002A|0020" ;
Min["Slovak (L)"]			="003B|002B|013E|0161|010D|0165|017E|00FD|00E1|00ED|00E9|003D|00B4|0071|0077|0065|0072|0074|007A|0075|0069|006F|0070|00FA|00E4|0061|0073|0064|0066|0067|0068|006A|006B|006C|00F4|00A7|0079|0078|0063|0076|0062|006E|006D|002C|002E|002D|003E|005D|00F3|0148|0026|0020" ;
Maj["Spanish (L)"]			="00AA|0021|0022|00B7|0024|0025|0026|002F|0028|0029|003D|003F|00BF|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|005E|00A8|0041|0053|0044|0046|0047|0048|004A|004B|004C|00D1|00C7|005A|0058|0043|0056|0042|004E|004D|003B|003A|005F|003E|007C|0040|0023|007E|002A" ;
Min["Spanish (L)"]			="00BA|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|0027|00A1|0071|0077|0065|0072|0074|0079|0075|0069|006F|0070|0060|00B4|0061|0073|0064|0066|0067|0068|006A|006B|006C|00F1|00E7|007A|0078|0063|0076|0062|006E|006D|002C|002E|002D|003C|005C|0040|0023|007E|002B" ;
Maj["Ukrainian (C)"]		="0401|0021|0040|0023|2116|0025|005E|0026|002A|0028|0029|005F|002B|0419|0426|0423|041A|0415|041D|0413|0428|0429|0417|0425|0407|0424|0406|0412|0410|041F|0420|041E|041B|0414|0416|0404|0490|042F|0427|0421|041C|0418|0422|042C|0411|042E|002E|003A|0022|003C|003E|003F" ;
Min["Ukrainian (C)"]		="0451|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0439|0446|0443|043A|0435|043D|0433|0448|0449|0437|0445|0457|0444|0456|0432|0430|043F|0440|043E|043B|0434|0436|0454|0491|044F|0447|0441|043C|0438|0442|044C|0431|044E|002C|003B|0027|007B|007D|002F" ;
Maj["Vietnamese (L)"]		="007E|0021|0040|0023|0024|0025|005E|0026|002A|0028|0029|005F|002B|0051|0057|0045|0052|0054|0059|0055|0049|004F|0050|01AF|01A0|0041|0053|0044|0046|0047|0048|004A|004B|004C|0102|00C2|005A|0058|0043|0056|0042|004E|004D|00CA|00D4|0110|003C|003E|003F|007D|003A|0022" ;
Min["Vietnamese (L)"]		="20AB|0031|0032|0033|0034|0035|0036|0037|0038|0039|0030|002D|003D|0071|0077|0065|0072|0074|0079|0075|0069|006F|0070|01B0|01A1|0061|0073|0064|0066|0067|0068|006A|006B|006C|0103|00E2|007A|0078|0063|0076|0062|006E|006D|00EA|00F4|0111|002C|002E|002F|007B|003B|0027" ;