import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Academic palette for charts
window.iEvalChartColors = {
    ink:    '#181d2d',
    saffron:'#e9a91a',
    clay:   '#b35d3d',
    paper:  '#f5efe3',
    muted:  '#a0a8b8',
    success:'#059669',
    danger: '#b35d3d',
};

Alpine.start();
