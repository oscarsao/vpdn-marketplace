// ═══════════════════════════════════════════════════════════
// COYAG VPDN — Chart Defaults Composable
// Shared ApexCharts configuration for consistent styling
// ═══════════════════════════════════════════════════════════

export const COYAG_RED = '#A40E05'
export const COYAG_DARK = '#1E1E2D'

export const CHART_COLORS = [
  '#A40E05', // COYAG Red
  '#7367F0', // Purple
  '#28C76F', // Green
  '#FF9F43', // Orange
  '#00CFE8', // Cyan
  '#EA5455', // Light Red
  '#636363', // Gray
  '#4BC0C0', // Teal
  '#FF6384', // Pink
  '#36A2EB', // Blue
]

export function useChartDefaults() {
  const formatEUR = (val) => {
    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M'
    if (val >= 1000) return Math.round(val / 1000) + 'K'
    return Math.round(val).toLocaleString('es-ES')
  }

  const formatEURFull = (val) => {
    return new Intl.NumberFormat('es-ES', {
      style: 'currency',
      currency: 'EUR',
      maximumFractionDigits: 0,
    }).format(val)
  }

  const toolbarConfig = {
    show: true,
    tools: {
      download: true,
      selection: false,
      zoom: false,
      zoomin: false,
      zoomout: false,
      pan: false,
      reset: false,
    },
  }

  const baseChartConfig = {
    fontFamily: "'Inter', 'Segoe UI', sans-serif",
    foreColor: '#6B7280',
  }

  const gridConfig = {
    borderColor: '#F3F4F6',
    strokeDashArray: 4,
    xaxis: { lines: { show: false } },
    yaxis: { lines: { show: true } },
  }

  const apexLocaleES = {
    name: 'es',
    options: {
      months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      days: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
      shortDays: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
      toolbar: {
        download: 'Descargar',
        selection: 'Seleccion',
        selectionZoom: 'Zoom Seleccion',
        zoomIn: 'Acercar',
        zoomOut: 'Alejar',
        pan: 'Mover',
        reset: 'Resetear',
      },
    },
  }

  function makeBarOptions({
    categories = [],
    title = '',
    xLabel = '',
    yLabel = '',
    horizontal = false,
    distributed = false,
    colors = CHART_COLORS,
    formatY = formatEUR,
    height = 350,
    highlightIndex = -1,
  } = {}) {
    const barColors = highlightIndex >= 0
      ? categories.map((_, i) => i === highlightIndex ? COYAG_RED : '#E5E7EB')
      : colors

    return {
      chart: {
        type: 'bar',
        height,
        ...baseChartConfig,
        toolbar: toolbarConfig,
        locales: [apexLocaleES],
        defaultLocale: 'es',
      },
      plotOptions: {
        bar: {
          horizontal,
          borderRadius: 4,
          columnWidth: '60%',
          distributed,
        },
      },
      colors: distributed ? barColors : [colors[0]],
      dataLabels: { enabled: false },
      grid: gridConfig,
      xaxis: {
        categories,
        title: { text: xLabel, style: { fontSize: '12px', fontWeight: 600 } },
        labels: { style: { fontSize: '11px' }, rotate: -45, rotateAlways: categories.length > 6 },
      },
      yaxis: {
        title: { text: yLabel, style: { fontSize: '12px', fontWeight: 600 } },
        labels: { formatter: formatY },
      },
      tooltip: {
        y: { formatter: (val) => formatEURFull(val) },
      },
      title: title ? {
        text: title,
        style: { fontSize: '14px', fontWeight: 700, color: '#111827' },
      } : undefined,
      legend: { show: !distributed },
    }
  }

  function makeScatterOptions({
    title = '',
    xLabel = '',
    yLabel = '',
    height = 400,
    annotations = {},
  } = {}) {
    return {
      chart: {
        type: 'scatter',
        height,
        ...baseChartConfig,
        toolbar: { ...toolbarConfig, tools: { ...toolbarConfig.tools, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } },
        zoom: { enabled: true, type: 'xy' },
        locales: [apexLocaleES],
        defaultLocale: 'es',
      },
      colors: [COYAG_RED, '#7367F0', '#28C76F', '#FF9F43'],
      grid: gridConfig,
      xaxis: {
        title: { text: xLabel, style: { fontSize: '12px', fontWeight: 600 } },
        labels: { formatter: formatEUR },
        tickAmount: 8,
      },
      yaxis: {
        title: { text: yLabel, style: { fontSize: '12px', fontWeight: 600 } },
        labels: { formatter: (v) => formatEUR(v) + '/m2' },
      },
      tooltip: {
        custom: function({ seriesIndex, dataPointIndex, w }) {
          const point = w.config.series[seriesIndex].data[dataPointIndex]
          if (!point) return ''
          return `<div class="apexcharts-tooltip-custom" style="padding:8px 12px;font-size:12px;line-height:1.5">
            <b>${point.name || ''}</b><br/>
            Precio: ${formatEURFull(point.x)}<br/>
            EUR/m2: ${formatEURFull(point.y)}<br/>
            Superficie: ${point.size || 0} m2<br/>
            <span style="color:#999">${point.sector || ''}</span>
          </div>`
        },
      },
      markers: { size: 8, strokeWidth: 1, strokeColors: '#fff' },
      annotations,
      title: title ? {
        text: title,
        style: { fontSize: '14px', fontWeight: 700, color: '#111827' },
      } : undefined,
    }
  }

  return {
    COYAG_RED,
    CHART_COLORS,
    formatEUR,
    formatEURFull,
    toolbarConfig,
    baseChartConfig,
    gridConfig,
    apexLocaleES,
    makeBarOptions,
    makeScatterOptions,
  }
}
