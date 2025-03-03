import ReactEChartsCore from 'echarts-for-react/lib/core';
import * as echarts from 'echarts/core';
import { useAppContext } from 'providers/AppProvider';
import {
  GridComponent,
  LegendComponent,
  TitleComponent,
  TooltipComponent
} from 'echarts/components';
import { BarChart, LineChart } from 'echarts/charts';
import { CallbackDataParams } from 'echarts/types/dist/shared';
import { tooltipFormatterDefault } from 'helpers/echart-utils';
import { CanvasRenderer } from 'echarts/renderers';
import { useEffect, useState } from 'react';
import {
  reportCalAmountOfRewardForTheYear,
  reportHospitalAmountOfRewardForTheYear
} from '../../../services/ReportService';

echarts.use([
  TitleComponent,
  TooltipComponent,
  GridComponent,
  LineChart,
  CanvasRenderer,
  LegendComponent,
  BarChart
]);

const getDefaultOptions = (
  getThemeColor: (name: string) => string,
  total: number[] = [],
  calls = [],
  hospitals = []
) => ({
  color: getThemeColor('body-highlight-bg'),
  legend: {
    data: [
      {
        name: 'Вызовы',
        icon: 'roundRect',
        itemStyle: {
          color: getThemeColor('primary-light'),
          borderWidth: 0
        }
      },
      {
        name: 'Стационар',
        icon: 'roundRect',
        itemStyle: { color: getThemeColor('info-lighter'), borderWidth: 0 }
      },
      {
        name: 'Всего',
        icon: 'roundRect',
        itemStyle: { color: getThemeColor('primary'), borderWidth: 0 }
      }
    ],

    right: 'right',
    width: '100%',
    itemWidth: 16,
    itemHeight: 8,
    itemGap: 20,
    top: 3,
    inactiveColor: getThemeColor('quaternary-color'),
    inactiveBorderWidth: 0,
    textStyle: {
      color: getThemeColor('body-color'),
      fontWeight: 600,
      fontFamily: 'Nunito Sans'
    }
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'none'
    },
    padding: [7, 10],
    backgroundColor: getThemeColor('body-highlight-bg'),
    borderColor: getThemeColor('border-color'),
    textStyle: { color: getThemeColor('light-text-emphasis') },
    borderWidth: 1,
    transitionDuration: 0,
    formatter: (params: CallbackDataParams[]) => tooltipFormatterDefault(params)
  },
  xAxis: {
    type: 'category',
    data: getMonth(),
    show: true,
    boundaryGap: false,
    axisLine: {
      show: true,
      lineStyle: { color: getThemeColor('tertiary-bg') }
    },
    axisTick: {
      show: false
    },
    axisLabel: {
      // interval: 1,
      showMinLabel: false,
      showMaxLabel: false,
      color: getThemeColor('secondary-color'),
      formatter: (value: string) => value.slice(0, 3),
      fontFamily: 'Nunito Sans',
      fontWeight: 600,
      fontSize: 12.8
    },
    splitLine: {
      show: true,
      lineStyle: { color: getThemeColor('secondary-bg'), type: 'dashed' }
    }
  },
  yAxis: {
    type: 'value',
    boundaryGap: false,
    axisLabel: {
      showMinLabel: true,
      showMaxLabel: true,
      color: getThemeColor('secondary-color'),
      formatter: (value: number) => `${value.toLocaleString('ru-RU')}₽`,
      fontFamily: 'Nunito Sans',
      fontWeight: 600,
      fontSize: 12.8
    },
    splitLine: {
      show: true,
      lineStyle: { color: getThemeColor('secondary-bg') }
    }
  },
  series: [
    {
      name: 'Вызовы',
      type: 'line',
      data: calls,
      showSymbol: false,
      symbol: 'circle',
      symbolSize: 10,
      emphasis: {
        lineStyle: {
          width: 1
        }
      },
      lineStyle: {
        type: 'dashed',
        width: 1,
        color: getThemeColor('primary-light')
      },
      itemStyle: {
        borderColor: getThemeColor('primary-light'),
        borderWidth: 3
      }
    },
    {
      name: 'Стационар',
      type: 'line',
      data: hospitals,
      showSymbol: false,
      symbol: 'circle',
      symbolSize: 10,
      emphasis: {
        lineStyle: {
          width: 1
        }
      },
      lineStyle: {
        width: 1,
        color: getThemeColor('info-lighter')
      },
      itemStyle: {
        borderColor: getThemeColor('info-lighter'),
        borderWidth: 3
      }
    },
    {
      name: 'Всего',
      type: 'line',
      data: total,
      showSymbol: false,
      symbol: 'circle',
      symbolSize: 10,
      emphasis: {
        lineStyle: {
          width: 3
        }
      },
      lineStyle: {
        width: 3,
        color: getThemeColor('primary')
      },
      itemStyle: {
        borderColor: getThemeColor('primary'),
        borderWidth: 3
      }
    }
  ],
  grid: { left: 0, right: 8, top: '14%', bottom: 0, containLabel: true }
});

const getMonth = () => {
  const currentDate = new Date();
  const months = [];
  let start = currentDate.getMonth() + 1;
  if (start > 11) {
    start = 0;
  }

  for (let i = start; i < 12; i++) {
    const month = new Date(currentDate.getFullYear(), i).toLocaleString(
      'ru-RU',
      {
        month: 'long'
      }
    );

    months.push(month.charAt(0).toUpperCase() + month.slice(1));
  }

  for (let i = 0; i < start; i++) {
    const month = new Date(currentDate.getFullYear(), i).toLocaleString(
      'ru-RU',
      {
        month: 'long'
      }
    );

    months.push(month.charAt(0).toUpperCase() + month.slice(1));
  }
  return months;
};

const Chart = () => {
  const { getThemeColor } = useAppContext();

  const [total, setTotal] = useState<number[]>([]);
  const [hospitals, setHospitals] = useState([]);
  const [calls, setCalls] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      const callsResult = await reportCalAmountOfRewardForTheYear();

      if (typeof callsResult?.data === 'object') {
        setCalls(callsResult.data);
      }

      const hospitalsResult = await reportHospitalAmountOfRewardForTheYear();

      if (typeof hospitalsResult?.data === 'object') {
        setHospitals(hospitalsResult.data);
      }

      if (
        typeof hospitalsResult?.data === 'object' &&
        typeof callsResult?.data === 'object'
      ) {
        const totalResult: number[] = [];

        for (let i = 0; i < 12; i++) {
          totalResult.push(hospitalsResult?.data[i] + callsResult?.data[i]);
        }
        setTotal(totalResult);
      }
    };

    fetchData();
  }, []);

  return (
    <ReactEChartsCore
      echarts={echarts}
      option={getDefaultOptions(getThemeColor, total, calls, hospitals)}
      style={{ height: '300px', width: '100%' }}
    />
  );
};

export default Chart;
