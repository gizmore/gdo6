<?php
namespace GDO\Language;
use GDO\File\FileUtil;

/**
 * This class contains Language data.
 * English Name | Native Name | iso-639-3 | iso-639-1
 * 
 * @author gizmore
 */
final class LanguageData
{
	public static function onInstall()
	{
		$bulkData = array();
		foreach (self::getLanguages() as $data)
		{
			list($en, $native, $iso3, $iso2) = $data;
			if (FileUtil::isFile(GWF_PATH . 'GDO/Language/img/'.strtolower($iso2).'.png'))
			{
			  $bulkData[] = [strtolower($iso2)];
			}
		}
		
		$fields = [GDO_Language::table()->gdoColumn('lang_iso')];
		GDO_Language::bulkReplace($fields, $bulkData);
	}
	
	public static function getLanguages()
	{
		# English Name | Native Name | iso-639-3 | iso-639-1
		static $languages = array(
			array('English', 'English', 'eng', 'en'),
			array('German', 'Deutsch', 'ger', 'de'),
			array('French', 'Française', 'fre', 'fr'),
			array('Bulgarian', 'български език', 'bul', 'bg'),
			array('Brazil', 'Brazil', 'bra', 'br'),
			array('Spanish', 'español', 'spa', 'es'),
			array('Chinese', '汉语 / 漢語', 'chi', 'zh'),
			array('Croatian', 'hrvatski', 'cro', 'hr'),
			array('Albanian', 'Shqip', 'alb', 'sq'),
			array('Arabic', 'العربية', 'ara', 'ar'),
// 			array('Amazigh', '', 'ama', ''),
			array('Catalan', 'català', 'cat', 'ca'),
			array('Armenian', 'Հայերեն', 'arm', 'hy'),
			array('Azerbaijani', 'Azərbaycan / Азәрбајҹан / آذربایجان دیلی', 'aze', 'az'),
			array('Bengali', 'বাংলা',  'ben', 'bn'),
			array('Dutch', 'Nederlands', 'dut', 'nl'),
			array('Bosnian', 'bosanski/босански', 'bos', 'bs'),
			array('Serbian', 'Српски / Srpski ', 'ser', 'sr'),
			array('Portuguese', 'português', 'por', 'pt'),
			array('Greek', 'Ελληνικά / Ellīniká', 'gre', 'el'),
			array('Turkish', 'Türkçe', 'tur', 'tr'),
			array('Czech', 'Čeština', 'cze', 'cs'),
			array('Danish', 'dansk', 'dan', 'da'),
			array('Finnish', 'suomi', 'fin', 'fi'),
			array('Swedish', 'svenska', 'swe', 'sv'),
			array('Hungarian', 'magyar', 'hun', 'hu'),
			array('Icelandic', 'Íslenska', 'ice', 'is'),
			array('Hindi', 'हिन्दी / हिंदी',  'hin', 'hi'),
			array('Persian', 'فارسی', 'per', 'fa'),
			array('Kurdish', 'Kurdî / کوردی', 'kur', 'ku'),
			array('Irish', 'Gaeilge', 'iri', 'ga'),
			array('Hebrew', 'עִבְרִית / \'Ivrit', 'heb', 'he'),
			array('Italian', 'Italiano', 'ita', 'it'),
			array('Japanese', '日本語 / Nihongo', 'jap', 'ja'),
			array('Korean', '한국어 / 조선말',  'kor', 'ko'),
			array('Latvian', 'latviešu valoda', 'lat', 'lv'),
			array('Lithuanian', 'Lietuvių kalba', 'lit', 'lt'),
			array('Luxembourgish', 'Lëtzebuergesch', 'lux', 'lb'),
			array('Macedonian', 'Македонски јазик / Makedonski jazik', 'mac', 'mk'),
			array('Malay', 'Bahasa Melayu / بهاس ملايو', 'mal', 'ms'),
			array('Dhivehi', 'Dhivehi / Mahl', 'dhi', 'dv'),
// 			array("Montenegrin", "Црногорски / Crnogorski", "mon", ''),
			array('Maori', 'Māori', 'mao', 'mi'),
			array('Norwegian', 'norsk', 'nor', 'no'),
			array('Filipino', 'Filipino', 'fil', 'tl'),
			array('Polish', 'język polski', 'pol', 'pl'),
			array('Romanian', 'română / limba română', 'rom', 'ro'),
			array('Russian', 'Русский язык', 'rus', 'ru'),
			array('Slovak', 'slovenčina', 'slo', 'sk'),
			array('Mandarin', '官話 / Guānhuà', 'man', 'zh'),
			array('Tamil', 'தமிழ', 'tam', 'ta'),
			array('Slovene', 'slovenščina', 'slv', 'sl'),
			array('Zulu', 'isiZulu', 'zul', 'zu'),
			array('Xhosa', 'isiXhosa', 'xho', 'xh'),
			array('Afrikaans', 'Afrikaans', 'afr', 'af'),
// 			array('Northern Sotho', 'Sesotho sa Leboa', 'nso', '--'),
			array('Tswana', 'Setswana / Sitswana', 'tsw', 'tn'),
			array('Sotho', 'Sesotho', 'sot', 'st'),
			array('Tsonga', 'Tsonga', 'tso', 'ts'),
			array('Thai', 'ภาษาไทย / phasa thai', 'tha', 'th'),
			array('Ukrainian', 'українська мова', 'ukr', 'uk'),
			array('Vietnamese', 'Tiếng Việt', 'vie', 'vi'),
			array('Pashto', 'پښت', 'pas', 'ps'),
			array('Samoan', 'gagana Sāmoa', 'sam', 'sm'),
// 			array('Bajan', 'Barbadian Creole', 'baj', '--'),
			array('Belarusian', 'беларуская мова', 'bel', 'be'),
			array('Dzongkha', '', 'dzo', 'dz'),
// 			array('Quechua', '', 'que', ''),
// 			array('Aymara', '', 'aym', ''),
// 			array('Setswana', '', 'set', ''),
// 			array('Bruneian', '', 'bru', ''),
// 			array('Indigenous', '', 'ind', ''),
// 			array('Kirundi', '', 'kir', ''),
// 			array('Swahili', '', 'swa', ''),
// 			array('Khmer', '', 'khm', ''),
// 			array('Sango', '', 'san', ''),
// 			array('Lingala', '', 'lin', ''),
// 			array('Kongo/Kituba', '', 'kon', ''),
// 			array('Tshiluba', '', 'tsh', ''),
// 			array('Afar', '', 'afa', ''),
// 			array('Somali', '', 'som', ''),
// 			array('Fang', '', 'fan', ''),
// 			array('Bube', '', 'bub', ''),
// 			array('Annobonese', '', 'ann', ''),
// 			array('Tigrinya', '', 'tig', ''),
// 			array('Estonian', 'Eesti', 'est', 'et'),
// 			array('Amharic', '', 'amh', ''),
// 			array('Faroese', '', 'far', ''),
// 			array('Bau Fijian', '', 'bau', ''),
// 			array('Hindustani', '', 'hit', ''),
// 			array('Tahitian', '', 'tah', ''),
// 			array('Georgian', '', 'geo', ''),
// 			array('Greenlandic', '', 'grl', ''),
// 			array('Chamorro', '', 'cha', ''),
// 			array('Crioulo', '', 'cri', ''),
// 			array('Haitian Creole', '', 'hai', ''),
// 			array('Indonesian', '', 'inn', ''),
// 			array('Kazakh', '', 'kaz', ''),
// 			array('Gilbertese', '', 'gil', ''),
// 			array('Kyrgyz', '', 'kyr', ''),
// 			array('Lao', '', 'lao', ''),
// 			array('Southern Sotho', '', 'sso', ''),
// 			array('Malagasy', '', 'mag', ''),
// 			array('Chichewa', '', 'chw', ''),
// 			array('Maltese', '', 'mat', ''),
// 			array('Marshallese', '', 'mar', ''),
// 			array('Moldovan', '', 'mol', ''),
// 			array('Gagauz', '', 'gag', ''),
// 			array('Monegasque', '', 'moq', ''),
// 			array('Mongolian', '', 'mgl', ''),
// 			array('Burmese', '', 'bur', ''),
// 			array('Oshiwambo', '', 'osh', ''),
// 			array('Nauruan', '', 'nau', ''),
// 			array('Nepal', '', 'nep', ''),
// 			array('Papiamento', '', 'pap', ''),
// 			array('Niuean', '', 'niu', ''),
// 			array('Norfuk', '', 'nfk', ''),
// 			array('Carolinian', '', 'car', ''),
// 			array('Urdu', 'اردو', 'urd', 'ur'),
// 			array('Palauan', '', 'pal', ''),
// 			array('Tok Pisin', '', 'tok', ''),
// 			array('Hiri Motu', '', 'hir', ''),
// 			array('Guarani', '', 'gua', ''),
// 			array('Pitkern', '', 'pit', ''),
// 			array('Kinyarwanda', '', 'kin', ''),
// 			array('Antillean Creole', '', 'ant', ''),
// 			array('Wolof', '', 'wol', ''),
// 			array('Sinhala', '', 'sin', ''),
// 			array('Sranan Tongo', '', 'sra', ''),
// 			array('Swati', '', 'swt', ''),
// 			array('Syrian', '', 'syr', ''),
// 			array('Tajik', '', 'taj', ''),
// 			array('Tetum', '', 'tet', ''),
// 			array('Tokelauan', '', 'tol', ''),
// 			array('Tongan', '', 'ton', ''),
// 			array('Turkmen', '', 'tkm', ''),
// 			array('Uzbek', '', 'uzb', ''),
// 			array('Dari', '', 'dar', ''),
// 			array('Tuvaluan', '', 'tuv', ''),
// 			array('Bislama', '', 'bis', ''),
// 			array('Uvean', '', 'uve', ''),
// 			array('Futunan', '', 'fut', ''),
// 			array('Shona', '', 'sho', ''),
// 			array('Sindebele', '', 'sid', ''),
// 			array('Taiwanese', '', 'tai', ''),
// 			array('Manx', '', 'max', ''),
			array('Fanmglish', 'Famster', 'fam', 'xf'),
			array('Bot', 'BotJSON', 'bot', 'xb'),
			array('Ibdes', 'RFCBotJSON', 'ibd', 'xi'),
			array('Test Japanese', 'Test Japanese', 'ori', 'xo')
		);
		return $languages;
	}
}
?>