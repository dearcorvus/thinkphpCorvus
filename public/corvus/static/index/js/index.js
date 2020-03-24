/*****--- 日历操作部分 ---*****/

function $onload(fun) {
    if (document.all) {
        attachEvent("onload", fun)
    } else {
        addEventListener("load", fun)
    }
}

$onload(CreateCalendar);

function CreateCalendar() {
    function Is_Leap(year) {
        return (year % 100 == 0 ? res = (year % 400 == 0 ? 1 : 0) : res = (year % 4 == 0 ? 1 : 0));
    }
    var NowDate = new Date(); //获取当前时间

    var NowTime = NowDate.getFullYear() + "/" + (NowDate.getMonth() + 1) + "/" + NowDate.getDate();

    var NowDateAft = NowDate;
    NowDateAft = new Date(NowDate.getFullYear() + "/" + (NowDate.getMonth() + 1) + "/" + NowDate.getDate());
    var BlTime = $(".BlTime").text();
    BlTime = BlTime.substring(0, BlTime.indexOf("个"));
    // NowDateAft.setDate(NowDateAft.getDate() + parseInt(BlTime));

    for (var s = 0; s < 4; s++) {

        var Y = NowDate.getFullYear(); //获取年份
        var M = NowDate.getMonth(); //获取月份
        var Day = NowDate.getDate(); //获取当前日期
        var FirstDay = new Date(Y, M, 1); //获取某月的第一天
        var Week = FirstDay.getDay(); //获取某月的第一天是星期几
        var Mdays = new Array(31, 28 + Is_Leap(Y), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); //各月份的总天数

        var tab = $('<table class="TableDate" cellpadding="0" cellspacing="1" border="0" style="border-collapse: separate;"></table>');
        tab.appendTo($(".CalendarCon"));

        var MText = (M + 1) < 10 ? "0" + (M + 1) : (M + 1); //一位数时前面加"0"显示
        var trY = $('<tr><td class="TdYear" colspan="7">' + Y + '年' + MText + '月' + '</td></tr>');
        trY.appendTo(tab);
        var TrTotal = Math.ceil((Mdays[M] + Week) / 7); //表格所需要的行数
        for (var i = 0; i < TrTotal; i++) {
            var tr = $("<tr></tr>");
            tr.appendTo(tab);
            for (var j = 0; j < 7; j++) {
                var td;
                var Num = i * 7 + j; //单元格自然序列号
                var Rq = Num - Week + 1; //计算日期

                (Rq <= 0 || Rq > Mdays[M]) ? Rq = "&nbsp;": Rq = Num - Week + 1;
                var CurrentTime = Y + "/" + (M + 1) + "/" + Rq;
                if (CurrentTime == NowTime)
                    td = $('<td class="TdDay TdNowDay">' + "今" + '</td>');
                else {
                    if (Rq == "&nbsp;")
                        td = $('<td class="TdNullDay">' + Rq + '</td>');
                    else {
                        CurrentTime = new Date(CurrentTime);

                        if (new Date(CurrentTime) > NowDateAft)
                            td = $('<td class="ClickTdDay">' + Rq + '</td>');
                        else
                            td = $('<td class="TdDay">' + Rq + '</td>');
                    }
                }
                td.appendTo(tr);
            }
        }
        NowDate.setMonth(NowDate.getMonth() + 1);
    }
};

$onload(ClickTdDay);

function ClickTdDay() {
    $(".ClickTdDay").click(function() {
        var Time = $(this).text();
        alert(Time)
    });
}