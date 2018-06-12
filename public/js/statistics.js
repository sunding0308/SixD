var machineId = $("input[name = 'machine_id']").val();
function getStatisticsData(recent) {
    myChart1.showLoading();    //数据加载完之前先显示一段简单的loading动画
    myChart2.showLoading();
    raw_water_tds = [];
    pure_water_tds = [];
    salt_rejection_rate = [];
    dates = [];
    $.ajax({
        type: "post",
        async : true,
        url: "/api/get_statistics_data",
        data: {
            'machineId' : machineId,
            'recent' : recent
        },
        dataType: "json",
        success: function(result){
            if(result){
                // console.log(result);
                for(var i = 0 ; i < result.length; i++){
                    raw_water_tds.push(result[i].raw_water_tds);
                    pure_water_tds.push(result[i].pure_water_tds);
                    salt_rejection_rate.push(result[i].salt_rejection_rate);
                    dates.push(result[i].created_at.substring(0, 10));
                }
                myChart1.hideLoading();    //隐藏加载动画
                myChart2.hideLoading();    //隐藏加载动画
                myChart1.setOption({        //载入数据
                    xAxis: {
                        data: dates    //填入X轴数据
                    },
                    series: [
                        {
                            data:raw_water_tds
                        },
                        {
                            data:pure_water_tds
                        }
                    ]
                });
                myChart2.setOption({        //载入数据
                    xAxis: {
                        data: dates    //填入X轴数据
                    },
                    series: [
                        {
                            data:salt_rejection_rate
                        }
                    ]
                });
            }
        },
        error: function(errmsg) {
            alert("Ajax获取服务器数据出错了！"+ errmsg);
        }
    });
}

var myChart1 = echarts.init(document.getElementById('water_tds'));
var myChart2 = echarts.init(document.getElementById('salt_rejection_rate'));
option1 = {
    title: {
        text: '水质变化统计'
    },
    tooltip: {
        trigger: 'axis',
        formatter: "{a0} : {c0} ppm<br/>{a1} : {c1} ppm"
    },
    legend: {
        data:['原水TDS值','纯水TDS值']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {
                title: '保存'
            }
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: [],
        axisLabel: {
            rotate: 45
        }
    },
    yAxis: {
        type: 'value',
        axisLabel:{
            formatter:'{value} ppm'
        }
    },
    series: [
        {
            name:'原水TDS值',
            type:'line',
            data:[]
        },
        {
            name:'纯水TDS值',
            type:'line',
            data:[]
        }
    ]
};
option2 = {
    title: {
        text: '脱盐率变化统计'
    },
    tooltip: {
        trigger: 'axis',
        formatter: "{a0} : {c0} %"
    },
    legend: {
        data:['脱盐率']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {
                title: '保存'
            }
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: [],
        axisLabel: {
            rotate: 45
        }
    },
    yAxis: {
        type: 'value',
        max:100,//Y轴最大值 不写的话自动调节
        axisLabel:{
            formatter:'{value} %'
        }
    },
    series: [
        {
            name:'脱盐率',
            type:'line',
            stack: '总量',
            data:[],
            formatter:'{value} %'
        }
    ]
};

getStatisticsData('one_month');

// 使用刚指定的配置项和数据显示图表。
myChart1.setOption(option1);
myChart2.setOption(option2);

function filterDate(recent) {
    getStatisticsData(recent);
}