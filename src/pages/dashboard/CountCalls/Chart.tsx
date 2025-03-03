import ReactEChartsCore from 'echarts-for-react/lib/core';
import * as echarts from 'echarts/core';
import { useAppContext } from 'providers/AppProvider';
import { TooltipComponent } from 'echarts/components';
import { BarChart } from 'echarts/charts';
import { tooltipFormatterDefault } from 'helpers/echart-utils';
import { CallbackDataParams } from 'echarts/types/dist/shared';
import { getMonth } from '../getMonth';

echarts.use([TooltipComponent, BarChart]);

const getDefaultOptions = (
  getThemeColor: (name: string) => string,
  count: number[] = []
) => ({
  color: getThemeColor('body-highlight-bg'),
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
      showMinLabel: false,
      showMaxLabel: true,
      color: getThemeColor('secondary-color'),
      formatter: (value: number) => `${value.toLocaleString('ru-RU')}`,
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
      name: 'Всего',
      type: 'line',
      data: count,
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

const Chart = ({ data = [] }: { data: number[] }) => {
  const { getThemeColor } = useAppContext();

  return (
    <ReactEChartsCore
      echarts={echarts}
      option={getDefaultOptions(getThemeColor, data)}
      style={{ height: '180px', width: '100%' }}
    />
  );
};

export default Chart;
