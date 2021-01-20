/* 
 E-commerce dashboard custom js file
 */
$(document).ready(function () {
    $(function () {
        /*Line chart*/
        var chart = c3.generate({
            bindto: '#lineChart',
            data: {
                columns: [
                    ['Sony', 45, 56, 76, 50, 106, 145],
                    ['Samsung', 75, 35, 94, 65, 243, 167],
                    ['Apple', 53, 61, 102, 195, 156, 223]
                ],
                colors: {
                    Sony: '#23b7e5',
                    Samsung: '#4CAF50',
                    Apple: '#7986CB'
                }
            }
        });
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
        $('#datatable1').dataTable();
    });
});
