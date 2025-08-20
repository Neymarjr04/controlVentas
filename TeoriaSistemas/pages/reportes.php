<?php
  $direccion = "reporte";
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reportes - Sistema Abarrotes</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./styles/reportes.css" />
    <link rel="stylesheet" href="./styles/navbar.css">
    <script defer src="./js/index.js"></script>
    <script src="./js/reportes.js"></script>

  </head>
  <body>
    <?php include "components/aside.php" ?>
    <div class="container">
      <div class="header">
        <h1>
          <i class="fas fa-chart-bar"></i>
          Reportes y Estadísticas
        </h1>
        <div class="header-actions">
          <button class="btn btn-info" onclick="refreshData()">
            <i class="fas fa-sync"></i> Actualizar
          </button>
          <button class="btn btn-secondary" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Volver
          </button>
        </div>
      </div>

      <div class="filters-card">
        <div class="filters-row">
          <div class="form-group">
            <label>Fecha Inicio</label>
            <input type="date" id="fechaInicio" value="" />
          </div>
          <div class="form-group">
            <label>Fecha Fin</label>
            <input type="date" id="fechaFin" value="" />
          </div>
          <div class="form-group">
            <label>Periodo Rápido</label>
            <select id="periodoRapido" onchange="setPeriodoRapido()">
              <option value="">Personalizado</option>
              <option value="hoy">Hoy</option>
              <option value="ayer">Ayer</option>
              <option value="semana">Esta Semana</option>
              <option value="mes">Este Mes</option>
              <option value="trimestre">Este Trimestre</option>
              <option value="año">Este Año</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" onclick="loadReports()">
              <i class="fas fa-chart-line"></i> Generar Reporte
            </button>
          </div>
        </div>
      </div>

      <div class="loading" id="loadingIndicator">
        <i class="fas fa-spinner"></i>
        <p>Generando reportes...</p>
      </div>

      <div id="reportContent">
        <!-- Estadísticas Principales -->
        <div class="stats-grid">
          <div class="stat-card sales">
            <div class="stat-header">
              <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
            </div>
            <div class="stat-value" id="totalVentas">0</div>
            <div class="stat-label">Total Ventas</div>
            <div class="stat-change positive">
              <i class="fas fa-arrow-up"></i> +12% vs periodo anterior
            </div>
          </div>

          <div class="stat-card revenue">
            <div class="stat-header">
              <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
              </div>
            </div>
            <div class="stat-value" id="totalIngresos">S/ 0</div>
            <div class="stat-label">Total Ingresos</div>
            <div class="stat-change positive">
              <i class="fas fa-arrow-up"></i> +8% vs periodo anterior
            </div>
          </div>

          <div class="stat-card products">
            <div class="stat-header">
              <div class="stat-icon">
                <i class="fas fa-box"></i>
              </div>
            </div>
            <div class="stat-value" id="productosVendidos">0</div>
            <div class="stat-label">Productos Vendidos</div>
            <div class="stat-change positive">
              <i class="fas fa-arrow-up"></i> +15% vs periodo anterior
            </div>
          </div>

          <div class="stat-card customers">
            <div class="stat-header">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
            <div class="stat-value" id="ticketPromedio">S/ 0</div>
            <div class="stat-label">Ticket Promedio</div>
            <div class="stat-change negative">
              <i class="fas fa-arrow-down"></i> -3% vs periodo anterior
            </div>
          </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-grid">
          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Ventas por Día</h3>
              <button
                class="btn btn-info btn-sm"
                onclick="toggleChartType('ventasChart')"
              >
                <i class="fas fa-chart-line"></i> Cambiar Tipo
              </button>
            </div>
            <div class="chart-container">
              <canvas id="ventasChart"></canvas>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Métodos de Pago</h3>
            </div>
            <div class="chart-container">
              <canvas id="metodosPagoChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Tablas de Datos -->
        <div class="tables-grid">
          <div class="table-card">
            <div class="table-header">
              <h3 class="table-title">Productos Más Vendidos</h3>
            </div>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Ingresos</th>
                  </tr>
                </thead>
                <tbody id="productosVendidosTable">
                  <!-- Se llenará dinámicamente -->
                </tbody>
              </table>
            </div>
          </div>

          <div class="table-card">
            <div class="table-header">
              <h3 class="table-title">Resumen por Categoría</h3>
            </div>
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Categoría</th>
                    <th>Ventas</th>
                    <th>Ingresos</th>
                    <th>%</th>
                  </tr>
                </thead>
                <tbody id="categoriesTable">
                  <!-- Se llenará dinámicamente -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones de Exportación -->
      <div class="export-actions">
        <div>
          <h4>Exportar Datos</h4>
          <p>Descargar reportes en diferentes formatos</p>
        </div>
        <div style="display: flex; gap: 10px">
          <button class="btn btn-success" onclick="exportToPDF()">
            <i class="fas fa-file-pdf"></i> Exportar PDF
          </button>
          <button class="btn btn-primary" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i> Exportar Excel
          </button>
          <button class="btn btn-info" onclick="printReport()">
            <i class="fas fa-print"></i> Imprimir
          </button>
        </div>
      </div>
    </div>
  </body>
</html>
