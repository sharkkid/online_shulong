/**
 * Traditional Chinese translation for bootstrap-datepicker
 * Rung-Sheng Jang <daniel@i-trend.co.cc>
 * FrankWu  <frankwu100@gmail.com> Fix more appropriate use of Traditional Chinese habit
 */
;(function($){
	$.fn.datepicker.dates['zh-TW'] = {
		days: ["�P����", "�P���@", "�P���G", "�P���T", "�P���|", "�P����", "�P����"],
		daysShort: ["�g��", "�g�@", "�g�G", "�g�T", "�g�|", "�g��", "�g��"],
		daysMin:  ["��", "�@", "�G", "�T", "�|", "��", "��"],
		months: ["�@��", "�G��", "�T��", "�|��", "����", "����", "�C��", "�K��", "�E��", "�Q��", "�Q�@��", "�Q�G��"],
		monthsShort: ["1��", "2��", "3��", "4��", "5��", "6��", "7��", "8��", "9��", "10��", "11��", "12��"],
		today: "����",
		format: "yyyy�~mm��dd��",
		weekStart: 1,
		clear: "�M��"
	};
}(jQuery));