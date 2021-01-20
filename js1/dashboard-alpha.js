/* 
 Dashboard alpha custom js file
 */
$(document).ready(function () {
    $(function () {
        //page view chart
        c3.generate({
            bindto: '#stocked',
            data: {
                columns: [
                    ['出貨數量', 130, 200, 100, 140, 150, 250, 250, 250, 250, 250, 250, 250],
                    ['損耗數量', 50, 90, 80, 40, 55, 89, 89, 89, 89, 89, 89, 89]
                ],
                colors: {
                    出貨數量: '#23b7e5',
                    損耗數量: '#ddd'
                },
                type: 'bar',
                groups: [
                    ['出貨數量', '損耗數量']
                ]
            }
        });
        /*time series*/
        var chart = c3.generate({
            bindto: '#timeseriesChart',
            data: {
                x: 'x',
                xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
                columns: [
                    ['x', '2016-03-06', '2016-09-02', '2016-09-03', '2016-09-04', '2013-01-05', '2016-09-06'],
                    ['x', '20190101', '20190102', '20190103', '20190104', '20190105', '20190106'],
                    ['進貨', 30, 200, 100, 400, 150, 250],
                    ['出貨', 130, 340, 200, 500, 250, 350]
                ],
                colors: {
                    進貨: '#23b7e5',
                    出貨: '#BABABA',
                    消耗: '#26A69A'
                }
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: '%Y-%m-%d'
                    }
                }
            }
        });

        setTimeout(function () {
            chart.load({
                columns: [
                    ['耗損', 400, 500, 450, 700, 600, 500]
                ]
            });
        }, 1000);
        //pie chart
        c3.generate({
            bindto: '#pieChart',
            data: {
                columns: [
                    ['Remains', 30],
                    ['Used', 120]
                ],
                colors: {
                    Remains: '#F44336',
                    Used: '#50cdf4'
                },
                type: 'pie'
            }
        });
    });
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });
    // $.toast({
    //     heading: 'Welcome back Emily',
    //     text: 'A simple and easy to use bootstrap admin template',
    //     position: 'top-right',
    //     loaderBg: '#fff',
    //     icon: 'success',
    //     hideAfter: 3000,
    //     stack: 1
    // });

});