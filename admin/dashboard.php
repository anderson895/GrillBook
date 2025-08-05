<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurant Seating Chart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .seating-chart {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      grid-template-rows: repeat(10, 1fr);
      gap: 15px;
      height: 80vh;
      min-height: 600px;
    }

    .table {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 10px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      border: 2px solid transparent;
    }

    .table:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border-color: #26a69a;
    }

    .table-name {
      font-weight: 600;
      font-size: 14px;
      color: #333;
      margin-bottom: 4px;
    }

    .table-orders {
      font-size: 12px;
      color: #666;
    }

    .has-orders {
      background: #fff3e0;
      border-color: #ff9800;
    }

    .has-orders .table-name {
      color: #e65100;
    }

    .has-orders .table-orders {
      color: #f57c00;
      font-weight: 500;
    }

    /* Grid positioning for each table */
    .g6 { grid-column: 1; grid-row: 1; }
    .g5 { grid-column: 2; grid-row: 1; }
    .f3 { grid-column: 10; grid-row: 1; }
    .f4 { grid-column: 11; grid-row: 1; }

    .g4 { grid-column: 1; grid-row: 2; }
    .g3 { grid-column: 2; grid-row: 2; }
    .e4 { grid-column: 3; grid-row: 2; }
    .e8 { grid-column: 4; grid-row: 2; }
    .f1 { grid-column: 10; grid-row: 2; }
    .f2 { grid-column: 11; grid-row: 2; }

    .g2 { grid-column: 1; grid-row: 3; }
    .g1 { grid-column: 2; grid-row: 3; }
    .e3 { grid-column: 3; grid-row: 3; }
    .e7 { grid-column: 4; grid-row: 3; }

    .e2 { grid-column: 3; grid-row: 4; }
    .e6 { grid-column: 4; grid-row: 4; }
    .takeout1 { grid-column: 6; grid-row: 1; }
    .takeout2 { grid-column: 7; grid-row: 1; }
    .c6 { grid-column: 6; grid-row: 3; }
    .d6 { grid-column: 7; grid-row: 3; }
    .dj { grid-column: 10/12; grid-row: 3; }

    .a5 { grid-column: 1; grid-row: 5; }
    .b6 { grid-column: 2; grid-row: 5; }
    .e1 { grid-column: 3; grid-row: 5; }
    .e5 { grid-column: 4; grid-row: 5; }
    .c5 { grid-column: 6; grid-row: 4; }
    .d5 { grid-column: 7; grid-row: 4; }
    .soundect { grid-column: 10/12; grid-row: 4; }

    .a4 { grid-column: 1; grid-row: 6; }
    .b5 { grid-column: 2; grid-row: 6; }
    .c4 { grid-column: 6; grid-row: 5; }
    .d4 { grid-column: 7; grid-row: 5; }
    .acoustic { grid-column: 10/12; grid-row: 5; }

    .a3 { grid-column: 1; grid-row: 7; }
    .b4 { grid-column: 2; grid-row: 7; }
    .reserv { grid-column: 3/5; grid-row: 7; }
    .c3 { grid-column: 6; grid-row: 6; }
    .d3 { grid-column: 7; grid-row: 6; }
    .vip3 { grid-column: 10; grid-row: 6; }
    .vip2 { grid-column: 11; grid-row: 6; }

    .a2 { grid-column: 1; grid-row: 8; }
    .b3 { grid-column: 2; grid-row: 8; }
    .meeting { grid-column: 3/5; grid-row: 8; }
    .c2 { grid-column: 6; grid-row: 7; }
    .d2 { grid-column: 7; grid-row: 7; }
    .billiards { grid-column: 10/12; grid-row: 7; }

    .a1 { grid-column: 1; grid-row: 9; }
    .b2 { grid-column: 2; grid-row: 9; }
    .compli { grid-column: 3/5; grid-row: 9; }
    .c1 { grid-column: 6; grid-row: 8; }
    .d1 { grid-column: 7; grid-row: 8; }
    .vip1 { grid-column: 10/12; grid-row: 8; }

    .b1 { grid-column: 2; grid-row: 10; }

    .special-section {
      background: #f5f5f5;
      border: 2px dashed #999;
    }

    .takeout {
      background: #e8f5e8;
      border-color: #4caf50;
    }

    .takeout .table-name {
      color: #2e7d32;
    }

    @media (max-width: 768px) {
      .seating-chart {
        grid-template-columns: repeat(8, 1fr);
        grid-template-rows: repeat(15, 1fr);
        gap: 10px;
      }
      
      .table {
        padding: 8px;
      }
      
      .table-name {
        font-size: 12px;
      }
      
      .table-orders {
        font-size: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="seating-chart">
    <!-- Row 1 -->
    <div class="table g6">
      <div class="table-name">G6</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table g5">
      <div class="table-name">G5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table takeout takeout1">
      <div class="table-name">Take out 1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table takeout takeout2">
      <div class="table-name">Take out 2</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table f3">
      <div class="table-name">F3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table f4">
      <div class="table-name">F4</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 2 -->
    <div class="table g4">
      <div class="table-name">G4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table g3">
      <div class="table-name">G3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e4">
      <div class="table-name">E4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e8">
      <div class="table-name">E8</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table f1">
      <div class="table-name">F1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table f2">
      <div class="table-name">F2</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 3 -->
    <div class="table g2">
      <div class="table-name">G2</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table g1">
      <div class="table-name">G1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e3">
      <div class="table-name">E3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e7">
      <div class="table-name">E7</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table c6">
      <div class="table-name">C6</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table d6">
      <div class="table-name">D6</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section dj">
      <div class="table-name">DJ</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 4 -->
    <div class="table e2">
      <div class="table-name">E2</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e6">
      <div class="table-name">E6</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table c5">
      <div class="table-name">C5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table d5">
      <div class="table-name">D5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section soundect">
      <div class="table-name">SOUNDECT</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 5 -->
    <div class="table a5">
      <div class="table-name">A5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table b6">
      <div class="table-name">B6</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e1">
      <div class="table-name">E1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table e5">
      <div class="table-name">E5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table c4">
      <div class="table-name">C4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table d4">
      <div class="table-name">D4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section acoustic">
      <div class="table-name">ACOUSTIC</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 6 -->
    <div class="table a4">
      <div class="table-name">A4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table b5">
      <div class="table-name">B5</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table c3">
      <div class="table-name">C3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table d3">
      <div class="table-name">D3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table vip3">
      <div class="table-name">VIP 3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table vip2">
      <div class="table-name">VIP 2</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 7 -->
    <div class="table a3">
      <div class="table-name">A3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table b4">
      <div class="table-name">B4</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section reserv">
      <div class="table-name">RESERV.</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table has-orders c2">
      <div class="table-name">C2</div>
      <div class="table-orders">Orders: 1</div>
    </div>
    <div class="table d2">
      <div class="table-name">D2</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section billiards">
      <div class="table-name">BILLIARDS</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 8 -->
    <div class="table has-orders a2">
      <div class="table-name">A2</div>
      <div class="table-orders">Orders: 1</div>
    </div>
    <div class="table b3">
      <div class="table-name">B3</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section meeting">
      <div class="table-name">MEETING</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table c1">
      <div class="table-name">C1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table d1">
      <div class="table-name">D1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section vip1">
      <div class="table-name">VIP 1</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 9 -->
    <div class="table a1">
      <div class="table-name">A1</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table b2">
      <div class="table-name">B2</div>
      <div class="table-orders">Orders: 0</div>
    </div>
    <div class="table special-section compli">
      <div class="table-name">COMPLI</div>
      <div class="table-orders">Orders: 0</div>
    </div>

    <!-- Row 10 -->
    <div class="table has-orders b1">
      <div class="table-name">B1</div>
      <div class="table-orders">Orders: 1</div>
    </div>
  </div>
</body>
</html>
