function drawLines(type, data) {
  var id = 'chart-' + type;
  var chart = new CanvasJS.Chart(id, {
    title: {
      text: type
    },
    axisX: {
      valueFormatString: "MMM YYYY"
    },
    toolTip: {
      shared: true
    },
    legend: {
      cursor: "pointer",
      verticalAlign: "top",
      horizontalAlign: "center",
      dockInsidePlotArea: true
    },
    data: [
      {
        type: "line",
        name: "This Week",
        showInLegend: true,
        markerSize: 0,
        dataPoints: data[0]
      },
      {
        type: "line",
        name: "Last Week",
        showInLegend: true,
        markerSize: 0,
        dataPoints: data[1]
      },
      {
        type: "line",
        name: "Two Weeks Ago",
        showInLegend: true,
        markerSize: 0,
        dataPoints: data[2]
      },
      {
        type: "line",
        name: "Three Weeks Ago",
        showInLegend: true,
        markerSize: 0,
        dataPoints: data[3]
      },
    ]
  });

  chart.render();
}

function drawChartDifferences(type, differences, percentages) {
  var id = 'chart-' + type + '-diff';
  var chart = new CanvasJS.Chart(id, {
    toolTip: {
      shared: true
    },
    legend: {
      cursor: "pointer",
    },
    data: [
      {
        type: "column",
        name: "Last Week",
        showInLegend: true,
        dataPoints: differences[2]
      },
      {
        type: "line",
        name: "Percent",
        axisYType: "secondary",
        yValueFormatString: "# '%'",
        dataPoints: percentages[2]
      },
      {
        type: "column",
        name: "Two Weeks Ago",
        showInLegend: true,
        dataPoints: differences[1]
      },
      {
        type: "line",
        name: "Percent",
        axisYType: "secondary",
        yValueFormatString: "# '%'",
        dataPoints: percentages[1]
      },
      {
        type: "column",
        name: "Three Weeks Ago",
        showInLegend: true,
        dataPoints: differences[0]
      },
      {
        type: "line",
        name: "Percent",
        axisYType: "secondary",
        yValueFormatString: "# '%'",
        dataPoints: percentages[0]
      }
    ]
  });

  chart.render();
}

function drawChartWeekly(type, differences) {
  var id = 'chart-' + type + '-weekly';
  var chart = new CanvasJS.Chart(id, {
    toolTip: {
      shared: true
    },
    legend: {
      cursor: "pointer",
    },
    data: [
      {
        type: "column",
        name: "This Week",
        showInLegend: true,
        dataPoints: differences[0]
      },
      {
        type: "column",
        name: "Last Week",
        showInLegend: true,
        dataPoints: differences[1]
      },
      {
        type: "column",
        name: "Two Weeks Ago",
        showInLegend: true,
        dataPoints: differences[2]
      },
      {
        type: "column",
        name: "Three Weeks Ago",
        showInLegend: true,
        dataPoints: differences[3]
      }
    ]
  });

  chart.render();
}