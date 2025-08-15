// Variables globales para los gráficos
let ventasChart = null;
let metodosPagoChart = null;
let currentChartType = "line";

// Datos de ejemplo
const reportData = {
  ventas: [
    { fecha: "2024-08-01", ventas: 45, ingresos: 1250.5 },
    { fecha: "2024-08-02", ventas: 52, ingresos: 1380.25 },
    { fecha: "2024-08-03", ventas: 38, ingresos: 980.75 },
    { fecha: "2024-08-04", ventas: 61, ingresos: 1650.0 },
    { fecha: "2024-08-05", ventas: 49, ingresos: 1420.3 },
    { fecha: "2024-08-06", ventas: 55, ingresos: 1580.9 },
    { fecha: "2024-08-07", ventas: 67, ingresos: 1850.45 },
  ],
  metodosPago: [
    { metodo: "Efectivo", cantidad: 180, porcentaje: 52.3 },
    { metodo: "Tarjeta", cantidad: 98, porcentaje: 28.5 },
    { metodo: "Yape", cantidad: 45, porcentaje: 13.1 },
    { metodo: "Plin", cantidad: 21, porcentaje: 6.1 },
  ],
  productosMasVendidos: [
    { nombre: "Coca Cola 500ml", cantidad: 128, ingresos: 448.0 },
    { nombre: "Pan Integral", cantidad: 95, ingresos: 399.0 },
    { nombre: "Leche Gloria UHT", cantidad: 87, ingresos: 417.6 },
    { nombre: "Aceite Primor 1L", cantidad: 65, ingresos: 552.5 },
    { nombre: "Detergente Ariel", cantidad: 54, ingresos: 696.6 },
  ],
  categorias: [
    { categoria: "Abarrotes", ventas: 245, ingresos: 3250.8, porcentaje: 45.2 },
    { categoria: "Bebidas", ventas: 189, ingresos: 2180.5, porcentaje: 30.3 },
    { categoria: "Limpieza", ventas: 98, ingresos: 1250.2, porcentaje: 17.4 },
    { categoria: "Snacks", ventas: 67, ingresos: 498.9, porcentaje: 6.9 },
    { categoria: "Lácteos", ventas: 45, ingresos: 378.6, porcentaje: 5.3 },
  ],
};

// Inicializar la aplicación
document.addEventListener("DOMContentLoaded", function () {
  setupDates();
  loadReports();
});

function setupDates() {
  const today = new Date();
  const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

  document.getElementById("fechaInicio").value = lastMonth
    .toISOString()
    .split("T")[0];
  document.getElementById("fechaFin").value = today.toISOString().split("T")[0];
}

function setPeriodoRapido() {
  const periodo = document.getElementById("periodoRapido").value;
  const today = new Date();
  let fechaInicio, fechaFin;

  switch (periodo) {
    case "hoy":
      fechaInicio = fechaFin = today;
      break;
    case "ayer":
      const ayer = new Date(today);
      ayer.setDate(today.getDate() - 1);
      fechaInicio = fechaFin = ayer;
      break;
    case "semana":
      const inicioSemana = new Date(today);
      inicioSemana.setDate(today.getDate() - today.getDay());
      fechaInicio = inicioSemana;
      fechaFin = today;
      break;
    case "mes":
      fechaInicio = new Date(today.getFullYear(), today.getMonth(), 1);
      fechaFin = today;
      break;
    case "trimestre":
      const quarter = Math.floor(today.getMonth() / 3);
      fechaInicio = new Date(today.getFullYear(), quarter * 3, 1);
      fechaFin = today;
      break;
    case "año":
      fechaInicio = new Date(today.getFullYear(), 0, 1);
      fechaFin = today;
      break;
    default:
      return;
  }

  document.getElementById("fechaInicio").value = fechaInicio
    .toISOString()
    .split("T")[0];
  document.getElementById("fechaFin").value = fechaFin
    .toISOString()
    .split("T")[0];
}

function loadReports() {
  showLoading(true);

  // Simular carga de datos
  setTimeout(() => {
    updateStats();
    createCharts();
    updateTables();
    showLoading(false);
  }, 1500);
}

function showLoading(show) {
  const loading = document.getElementById("loadingIndicator");
  const content = document.getElementById("reportContent");

  if (show) {
    loading.style.display = "block";
    content.style.opacity = "0.5";
  } else {
    loading.style.display = "none";
    content.style.opacity = "1";
  }
}

function updateStats() {
  // Calcular estadísticas
  const totalVentas = reportData.ventas.reduce(
    (sum, item) => sum + item.ventas,
    0
  );
  const totalIngresos = reportData.ventas.reduce(
    (sum, item) => sum + item.ingresos,
    0
  );
  const productosVendidos = reportData.productosMasVendidos.reduce(
    (sum, item) => sum + item.cantidad,
    0
  );
  const ticketPromedio = totalIngresos / totalVentas;

  // Actualizar elementos
  document.getElementById("totalVentas").textContent =
    totalVentas.toLocaleString();
  document.getElementById(
    "totalIngresos"
  ).textContent = `S/ ${totalIngresos.toLocaleString("es-PE", {
    minimumFractionDigits: 2,
  })}`;
  document.getElementById("productosVendidos").textContent =
    productosVendidos.toLocaleString();
  document.getElementById(
    "ticketPromedio"
  ).textContent = `S/ ${ticketPromedio.toFixed(2)}`;
}

function createCharts() {
  createVentasChart();
  createMetodosPagoChart();
}

function createVentasChart() {
  const ctx = document.getElementById("ventasChart").getContext("2d");

  if (ventasChart) {
    ventasChart.destroy();
  }

  const labels = reportData.ventas.map((item) => {
    const fecha = new Date(item.fecha);
    return fecha.toLocaleDateString("es-PE", {
      day: "2-digit",
      month: "2-digit",
    });
  });

  ventasChart = new Chart(ctx, {
    type: currentChartType,
    data: {
      labels: labels,
      datasets: [
        {
          label: "Ventas",
          data: reportData.ventas.map((item) => item.ventas),
          borderColor: "rgba(102, 126, 234, 1)",
          backgroundColor:
            currentChartType === "bar"
              ? "rgba(102, 126, 234, 0.8)"
              : "rgba(102, 126, 234, 0.1)",
          borderWidth: 2,
          fill: currentChartType === "line",
          tension: 0.4,
        },
        {
          label: "Ingresos (S/)",
          data: reportData.ventas.map((item) => item.ingresos),
          borderColor: "rgba(40, 167, 69, 1)",
          backgroundColor:
            currentChartType === "bar"
              ? "rgba(40, 167, 69, 0.8)"
              : "rgba(40, 167, 69, 0.1)",
          borderWidth: 2,
          fill: currentChartType === "line",
          tension: 0.4,
          yAxisID: "y1",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
      scales: {
        y: {
          type: "linear",
          display: true,
          position: "left",
          title: {
            display: true,
            text: "Número de Ventas",
          },
        },
        y1: {
          type: "linear",
          display: true,
          position: "right",
          title: {
            display: true,
            text: "Ingresos (S/)",
          },
          grid: {
            drawOnChartArea: false,
          },
        },
      },
      interaction: {
        mode: "nearest",
        axis: "x",
        intersect: false,
      },
    },
  });
}

function createMetodosPagoChart() {
  const ctx = document.getElementById("metodosPagoChart").getContext("2d");

  if (metodosPagoChart) {
    metodosPagoChart.destroy();
  }

  metodosPagoChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: reportData.metodosPago.map((item) => item.metodo),
      datasets: [
        {
          data: reportData.metodosPago.map((item) => item.cantidad),
          backgroundColor: [
            "rgba(40, 167, 69, 0.8)",
            "rgba(0, 123, 255, 0.8)",
            "rgba(255, 193, 7, 0.8)",
            "rgba(220, 53, 69, 0.8)",
          ],
          borderColor: [
            "rgba(40, 167, 69, 1)",
            "rgba(0, 123, 255, 1)",
            "rgba(255, 193, 7, 1)",
            "rgba(220, 53, 69, 1)",
          ],
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = context.parsed;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((value / total) * 100).toFixed(1);
              return `${label}: ${value} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}

function updateTables() {
  updateProductosVendidosTable();
  updateCategoriasTable();
}

function updateProductosVendidosTable() {
  const tbody = document.getElementById("productosVendidosTable");
  tbody.innerHTML = reportData.productosMasVendidos
    .map(
      (producto, index) => `
                <tr>
                    <td><strong>${index + 1}</strong></td>
                    <td class="product-name">${producto.nombre}</td>
                    <td>
                        <span class="quantity-badge">${producto.cantidad}</span>
                    </td>
                    <td class="revenue-text">S/ ${producto.ingresos.toFixed(
                      2
                    )}</td>
                </tr>
            `
    )
    .join("");
}

function updateCategoriasTable() {
  const tbody = document.getElementById("categoriesTable");
  tbody.innerHTML = reportData.categorias
    .map(
      (categoria) => `
                <tr>
                    <td class="product-name">${categoria.categoria}</td>
                    <td>
                        <span class="quantity-badge">${categoria.ventas}</span>
                    </td>
                    <td class="revenue-text">S/ ${categoria.ingresos.toFixed(
                      2
                    )}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 60px; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                                <div style="width: ${
                                  categoria.porcentaje
                                }%; height: 100%; background: linear-gradient(45deg, #667eea, #764ba2);"></div>
                            </div>
                            <span style="font-size: 12px; font-weight: 600;">${
                              categoria.porcentaje
                            }%</span>
                        </div>
                    </td>
                </tr>
            `
    )
    .join("");
}

function toggleChartType(chartName) {
  if (chartName === "ventasChart") {
    currentChartType = currentChartType === "line" ? "bar" : "line";
    createVentasChart();
  }
}

function refreshData() {
  showLoading(true);

  // Simular actualización de datos
  setTimeout(() => {
    // Aquí iría la llamada real a la API
    loadReports();
  }, 1000);
}

function exportToPDF() {
  // Simular exportación a PDF
  const loading = document.createElement("div");
  loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
  loading.style.cssText =
    "position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; color: #333;";
  document.body.appendChild(loading);

  setTimeout(() => {
    document.body.removeChild(loading);

    // Simular descarga
    const link = document.createElement("a");
    link.href =
      "data:application/pdf;base64,JVBERi0xLjQKJdPr6eEKMSAwIG9iago..."; // PDF simulado
    link.download = `reporte-ventas-${
      new Date().toISOString().split("T")[0]
    }.pdf`;
    link.click();

    alert("Reporte PDF generado exitosamente");
  }, 2000);
}

function exportToExcel() {
  // Simular exportación a Excel
  const loading = document.createElement("div");
  loading.innerHTML =
    '<i class="fas fa-spinner fa-spin"></i> Generando Excel...';
  loading.style.cssText =
    "position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; color: #333;";
  document.body.appendChild(loading);

  setTimeout(() => {
    document.body.removeChild(loading);

    // Crear CSV simulado (más simple que Excel real)
    let csvContent = "Producto,Cantidad Vendida,Ingresos\n";
    reportData.productosMasVendidos.forEach((producto) => {
      csvContent += `"${producto.nombre}",${
        producto.cantidad
      },${producto.ingresos.toFixed(2)}\n`;
    });

    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `reporte-productos-${
      new Date().toISOString().split("T")[0]
    }.csv`;
    link.click();

    alert("Reporte Excel generado exitosamente");
  }, 1500);
}

function printReport() {
  // Crear ventana de impresión
  const printWindow = window.open("", "_blank");

  const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Reporte de Ventas - Abarrotes Don José</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px; 
                            color: #333; 
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 30px; 
                            border-bottom: 2px solid #333; 
                            padding-bottom: 15px; 
                        }
                        .stats { 
                            display: flex; 
                            justify-content: space-around; 
                            margin: 30px 0; 
                        }
                        .stat-item { 
                            text-align: center; 
                            border: 1px solid #ddd; 
                            padding: 15px; 
                            border-radius: 8px; 
                        }
                        .stat-value { 
                            font-size: 24px; 
                            font-weight: bold; 
                            color: #28a745; 
                        }
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin: 20px 0; 
                        }
                        th, td { 
                            border: 1px solid #ddd; 
                            padding: 8px; 
                            text-align: left; 
                        }
                        th { 
                            background-color: #f2f2f2; 
                            font-weight: bold; 
                        }
                        .footer { 
                            margin-top: 40px; 
                            text-align: center; 
                            font-size: 12px; 
                            color: #666; 
                        }
                        @media print {
                            .no-print { display: none; }
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Abarrotes Don José</h1>
                        <h2>Reporte de Ventas</h2>
                        <p>Periodo: ${
                          document.getElementById("fechaInicio").value
                        } al ${document.getElementById("fechaFin").value}</p>
                    </div>
                    
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value">${
                              document.getElementById("totalVentas").textContent
                            }</div>
                            <div>Total Ventas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${
                              document.getElementById("totalIngresos")
                                .textContent
                            }</div>
                            <div>Total Ingresos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${
                              document.getElementById("productosVendidos")
                                .textContent
                            }</div>
                            <div>Productos Vendidos</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${
                              document.getElementById("ticketPromedio")
                                .textContent
                            }</div>
                            <div>Ticket Promedio</div>
                        </div>
                    </div>
                    
                    <h3>Productos Más Vendidos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${reportData.productosMasVendidos
                              .map(
                                (producto, index) => `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${producto.nombre}</td>
                                    <td>${producto.cantidad}</td>
                                    <td>S/ ${producto.ingresos.toFixed(2)}</td>
                                </tr>
                            `
                              )
                              .join("")}
                        </tbody>
                    </table>
                    
                    <h3>Resumen por Categoría</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Ventas</th>
                                <th>Ingresos</th>
                                <th>Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${reportData.categorias
                              .map(
                                (categoria) => `
                                <tr>
                                    <td>${categoria.categoria}</td>
                                    <td>${categoria.ventas}</td>
                                    <td>S/ ${categoria.ingresos.toFixed(2)}</td>
                                    <td>${categoria.porcentaje}%</td>
                                </tr>
                            `
                              )
                              .join("")}
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        <p>Reporte generado el ${new Date().toLocaleString(
                          "es-PE"
                        )}</p>
                        <p>Sistema de Ventas - Abarrotes Don José</p>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    </script>
                </body>
                </html>
            `;

  printWindow.document.write(printContent);
  printWindow.document.close();
}

// Eventos de teclado
document.addEventListener("keydown", function (e) {
  if (e.ctrlKey && e.key === "r") {
    e.preventDefault();
    refreshData();
  }
  if (e.ctrlKey && e.key === "p") {
    e.preventDefault();
    printReport();
  }
});

console.log("Teclas de acceso rápido:");
console.log("Ctrl+R - Actualizar datos");
console.log("Ctrl+P - Imprimir reporte");
