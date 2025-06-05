<div class="container analitiche-container">

    <!-- Selettore per le sezioni -->
    <div class="section-selector mb-4">
        <div class="d-flex justify-content-center align-items-center w-100">
            <button class="section-tab active" onclick="switchCategory('overview')">
                <i data-lucide="chart-pie" class="section-icon"></i> Overview
            </button>
            <button class="section-tab" onclick="switchCategory('sales')">
                <i data-lucide="dollar-sign" class="section-icon"></i> Sales
            </button>
            <button class="section-tab" onclick="switchCategory('customers')">
                <i data-lucide="users" class="section-icon"></i> Customers
            </button>
            <button class="section-tab" onclick="switchCategory('operations')">
                <i data-lucide="chart-line" class="section-icon"></i> Operations
            </button>
        </div>
    </div>

    <!-- Contenuto delle Analitiche -->
    <div class="row analytics-content">
        <!-- Overview -->
        <div class="col-12 overview-content" id="overview">
            <div class="row">
                <!-- Card: Revenue -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card revenue-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle blue me-3">
                                <i data-lucide="euro" class="card-icon revenue-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">REVENUE</h6>
                                <h5 class="card-value mb-1">$0.00</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 12.5%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Orders -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card orders-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle orange me-3">
                                <i data-lucide="shopping-cart" class="card-icon orders-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">ORDERS</h6>
                                <h5 class="card-value mb-1">3</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 3.7%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Avg. Order -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card avg-order-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle purple me-3">
                                <i data-lucide="trending-up" class="card-icon avg-order-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">AVG. ORDER</h6>
                                <h5 class="card-value mb-1">$0.00</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 5.2%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Service Time -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card service-time-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle pink me-3">
                                <i data-lucide="clock" class="card-icon service-time-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">SERVICE TIME</h6>
                                <h5 class="card-value mb-1">24 min</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 3.1%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Trend Graph (Line Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">REVENUE TREND</h6>
                            <canvas id="revenueTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Orders Per Day (Bar Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">ORDERS PER DAY</h6>
                            <canvas id="ordersPerDayChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue Distribution (Pie Chart) -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">REVENUE DISTRIBUTION</h6>
                            <canvas id="revenueDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales -->
        <div class="col-12 sales-content" id="sales" style="display: none;">
            <div class="row">
                <!-- Card: Total Sales -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card total-sales-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle blue me-3">
                                <i data-lucide="dollar-sign" class="card-icon total-sales-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">TOTAL SALES</h6>
                                <h5 class="card-value mb-1">$1,200</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 10.0%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Items Sold -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card items-sold-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle orange me-3">
                                <i data-lucide="package" class="card-icon items-sold-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">ITEMS SOLD</h6>
                                <h5 class="card-value mb-1">25</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 6.5%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Conversion Rate -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card conversion-rate-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle purple me-3">
                                <i data-lucide="percent" class="card-icon conversion-rate-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">CONVERSION RATE</h6>
                                <h5 class="card-value mb-1">35%</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 2.1%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Avg. Transaction Time -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card transaction-time-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle pink me-3">
                                <i data-lucide="timer" class="card-icon transaction-time-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">AVG. TRANSACTION TIME</h6>
                                <h5 class="card-value mb-1">5 min</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 1.5%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Revenue Trend (Line Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">SALES REVENUE TREND</h6>
                            <canvas id="salesRevenueTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Sales Revenue Distribution (Pie Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">SALES REVENUE DISTRIBUTION</h6>
                            <canvas id="salesRevenueDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="col-12 customers-content" id="customers" style="display: none;">
            <div class="row">
                <!-- Card: New Customers -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card new-customers-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle blue me-3">
                                <i data-lucide="user-plus" class="card-icon new-customers-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">NEW CUSTOMERS</h6>
                                <h5 class="card-value mb-1">15</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 8.3%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Returning Customers -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card returning-customers-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle orange me-3">
                                <i data-lucide="user-check" class="card-icon returning-customers-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">RETURNING CUSTOMERS</h6>
                                <h5 class="card-value mb-1">45</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 5.0%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Customer Satisfaction -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card satisfaction-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle purple me-3">
                                <i data-lucide="smile" class="card-icon satisfaction-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">CUSTOMER SATISFACTION</h6>
                                <h5 class="card-value mb-1">4.2/5</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 0.3%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Avg. Visit Frequency -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card visit-frequency-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle pink me-3">
                                <i data-lucide="calendar" class="card-icon visit-frequency-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">AVG. VISIT FREQUENCY</h6>
                                <h5 class="card-value mb-1">2.5/week</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 1.2%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Growth Trend (Line Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">CUSTOMER GROWTH TREND</h6>
                            <canvas id="customerGrowthTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Customer KPIs -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">CUSTOMER KPIS</h6>
                            <div class="row">
                                <!-- Card: Customer Retention -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card kpi-card">
                                        <div class="card-body text-center">
                                            <i data-lucide="user-check" class="kpi-icon blue mb-2"></i>
                                            <h6 class="kpi-title mb-1">Customer Retention</h6>
                                            <h5 class="kpi-value mb-1">68%</h5>
                                            <span class="kpi-trend up">+5% vs last month</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Avg. Spend per Visit -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card kpi-card">
                                        <div class="card-body text-center">
                                            <i data-lucide="credit-card" class="kpi-icon orange mb-2"></i>
                                            <h6 class="kpi-title mb-1">Avg. Spend per Visit</h6>
                                            <h5 class="kpi-value mb-1">$42.50</h5>
                                            <span class="kpi-trend up">+$32.0 vs last month</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Discount Usage -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card kpi-card">
                                        <div class="card-body text-center">
                                            <i data-lucide="percent" class="kpi-icon purple mb-2"></i>
                                            <h6 class="kpi-title mb-1">Discount Usage</h6>
                                            <h5 class="kpi-value mb-1">23%</h5>
                                            <span class="kpi-trend neutral">of orders</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card: Group Size -->
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <div class="card kpi-card">
                                        <div class="card-body text-center">
                                            <i data-lucide="users" class="kpi-icon pink mb-2"></i>
                                            <h6 class="kpi-title mb-1">Group Size</h6>
                                            <h5 class="kpi-value mb-1">3.2</h5>
                                            <span class="kpi-trend neutral">people per table</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operations -->
        <div class="col-12 operations-content" id="operations" style="display: none;">
            <div class="row">
                <!-- Card: Inventory Turnover -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card inventory-turnover-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle blue me-3">
                                <i data-lucide="refresh-cw" class="card-icon inventory-turnover-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">INVENTORY TURNOVER</h6>
                                <h5 class="card-value mb-1">3.5</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 4.2%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Staff Efficiency -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card staff-efficiency-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle orange me-3">
                                <i data-lucide="users" class="card-icon staff-efficiency-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">STAFF EFFICIENCY</h6>
                                <h5 class="card-value mb-1">10</h5>
                                <span class="card-trend up">
                                    <i data-lucide="trending-up" class="trend-icon"></i> 2.5%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Downtime -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card downtime-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle purple me-3">
                                <i data-lucide="clock" class="card-icon downtime-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">DOWNTIME</h6>
                                <h5 class="card-value mb-1">15 min</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 1.8%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Operational Cost -->
                <div class="col-md-6 mb-4">
                    <div class="card analytics-card operational-cost-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-circle pink me-3">
                                <i data-lucide="dollar-sign" class="card-icon operational-cost-icon"></i>
                            </div>
                            <div>
                                <h6 class="card-title mb-1">OPERATIONAL COST</h6>
                                <h5 class="card-value mb-1">$500</h5>
                                <span class="card-trend down">
                                    <i data-lucide="trending-down" class="trend-icon"></i> 3.0%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operational Efficiency Trend (Line Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">OPERATIONAL EFFICIENCY TREND</h6>
                            <canvas id="operationalEfficiencyTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Downtime Per Day (Bar Chart) -->
                <div class="col-12 mb-4">
                    <div class="card analytics-card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">DOWNTIME PER DAY</h6>
                            <canvas id="downtimePerDayChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stili CSS -->
<style>
    /* Stili generali per il container analitiche */
    .analitiche-container {
        background-color: #121212;
        color: white;
        padding: 20px;
        border-radius: 10px;
        position: relative;
    }

    .analitiche-container h1 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        color: white;
    }

    /* Stili per il selettore delle sezioni */
    .section-selector {
        background-color: #1e1e1e;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        position: static;
        top: 0;
        z-index: 500;
    }

    .section-selector .d-flex {
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .section-tab {
        display: flex;
        align-items: center;
        background-color: #1e1e1e;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        min-width: 220px;
        height: 50px;
        justify-content: center;
    }

    .section-tab:hover {
        background-color: #3a3a3a;
        transform: translateY(-2px);
    }

    .section-tab.active {
        background-color: rgba(0, 255, 127, 0.2);
        color: #00FF7F;
        font-weight: 700;
    }

    .section-icon {
        width: 22px;
        height: 22px;
        margin-right: 8px;
        color: #00FF7F;
    }

    /* Stili per le card analitiche */
    .analytics-card {
        background-color: #1e1e1e;
        border: none;
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        min-height: 140px;
        position: relative;
        overflow: hidden;
    }

    /* Colored border on the left using ::before */
    .analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -5px;
        width: 10px;
        height: 100%;
        z-index: 0;
    }

    /* Specific colors for each card */
    .revenue-card::before,
    .new-customers-card::before,
    .total-sales-card::before,
    .inventory-turnover-card::before {
        background-color: #0EA5E9;
    }

    .orders-card::before,
    .returning-customers-card::before,
    .items-sold-card::before,
    .staff-efficiency-card::before {
        background-color: #F97316;
    }

    .avg-order-card::before,
    .satisfaction-card::before,
    .conversion-rate-card::before,
    .downtime-card::before {
        background-color: #A855F7;
    }

    .service-time-card::before,
    .visit-frequency-card::before,
    .transaction-time-card::before,
    .operational-cost-card::before {
        background-color: #EC4899;
    }

    .analytics-card .card-body {
        padding: 25px;
        position: relative;
        z-index: 1;
    }

    /* Adjust card width to align with the graph */
    .col-md-6 .analytics-card {
        width: calc(100% + 5px);
        margin-left: -5px;
    }

    /* Ensure the graph card doesn't have the border adjustment */
    .col-12 .analytics-card {
        width: 100%;
        margin-left: 0;
    }

    .analytics-card .card-title {
        font-size: 14px;
        font-weight: 500;
        color: #a0a0a0;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .analytics-card .card-value {
        font-size: 28px;
        font-weight: 600;
        color: white;
        margin-bottom: 8px;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-circle.blue {
        background-color: rgba(14, 165, 233, 0.2);
    }

    .icon-circle.orange {
        background-color: rgba(249, 115, 22, 0.2);
    }

    .icon-circle.purple {
        background-color: rgba(168, 85, 247, 0.2);
    }

    .icon-circle.pink {
        background-color: rgba(236, 72, 153, 0.2);
    }

    .card-icon {
        width: 24px;
        height: 24px;
    }

    /* Specific icon colors for each card */
    .revenue-icon,
    .new-customers-icon,
    .total-sales-icon,
    .inventory-turnover-icon {
        color: #0EA5E9;
    }

    .orders-icon,
    .returning-customers-icon,
    .items-sold-icon,
    .staff-efficiency-icon {
        color: #F97316;
    }

    .avg-order-icon,
    .satisfaction-icon,
    .conversion-rate-icon,
    .downtime-icon {
        color: #A855F7;
    }

    .service-time-icon,
    .visit-frequency-icon,
    .transaction-time-icon,
    .operational-cost-icon {
        color: #EC4899;
    }

    .card-trend {
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .card-trend.up {
        color: #34c759;
    }

    .card-trend.down {
        color: #ff4d4f;
    }

    .trend-icon {
        width: 14px;
        height: 14px;
        margin-right: 4px;
    }

    /* Stili per i grafici */
    .analytics-card canvas {
        max-height: 200px;
    }

    /* Stili per le card KPI */
    .kpi-card {
        background-color: #1e1e1e;
        border: none;
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .kpi-icon {
        width: 24px;
        height: 24px;
    }

    .kpi-icon.blue {
        color: #0EA5E9;
    }

    .kpi-icon.orange {
        color: #F97316;
    }

    .kpi-icon.purple {
        color: #A855F7;
    }

    .kpi-icon.pink {
        color: #EC4899;
    }

    .kpi-title {
        font-size: 14px;
        font-weight: 500;
        color: #a0a0a0;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 24px;
        font-weight: 600;
        color: white;
    }

    .kpi-trend {
        font-size: 14px;
        font-weight: 500;
    }

    .kpi-trend.up {
        color: #34c759;
    }

    .kpi-trend.neutral {
        color: #a0a0a0;
    }
</style>

<!-- Script JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Funzione per switchare tra le sezioni
function switchCategory(category) {
    const sectionTabs = document.querySelectorAll('.section-tab');
    const contents = document.querySelectorAll('.analytics-content > div');

    // Aggiorna lo stato attivo dei tab
    sectionTabs.forEach(tab => tab.classList.remove('active'));
    const activeTab = document.querySelector(`.section-tab[onclick="switchCategory('${category}')"]`);
    if (activeTab) {
        activeTab.classList.add('active');
    }

    // Mostra il contenuto corrispondente
    contents.forEach(content => content.style.display = 'none');
    document.getElementById(category).style.display = 'block';

    // Qui puoi aggiungere logica per aggiornare i dati in base alla sezione selezionata
    console.log(`Sezione selezionata: ${category}`);
}

// Inizializzazione dei grafici con Chart.js
document.addEventListener("DOMContentLoaded", () => {
    // Inizializza le icone Lucide solo se non sono già state inizializzate
    if (typeof lucide !== 'undefined' && !window.lucideInitialized) {
        lucide.createIcons();
        window.lucideInitialized = true;
        console.log("Lucide caricato e inizializzato");
    } else if (!window.lucideInitialized) {
        console.error("Lucide non è stato caricato");
    }

    // Imposta la vista iniziale
    switchCategory('overview');

    // Configurazione del grafico Revenue Trend (Line Chart) - Overview
    const ctx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue',
                data: [5, 3, 4, 6, 5, 7, 6],
                borderColor: '#00FF7F', // Green
                backgroundColor: 'rgba(0, 255, 127, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#34c759',
                pointBorderColor: '#34c759',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Orders Per Day (Bar Chart) - Overview
    const barCtx = document.getElementById('ordersPerDayChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Orders',
                data: [3, 5, 2, 4, 6, 1, 3],
                backgroundColor: 'rgba(217, 70, 239, 0.2)',
                borderColor: '#D946EF', // Fuchsia
                borderWidth: 3
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Revenue Distribution (Pie Chart) - Overview
    const pieCtx = document.getElementById('revenueDistributionChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product D', 'Product D'],
            datasets: [{
                label: 'Revenue Distribution',
                data: [30, 25, 20, 20, 2, 3],
                backgroundColor: [
                    '#0EA5E9',
                    '#F97316',
                    '#A855F7',
                    '#EC4899',
                    '#EF4444',
                    '#06B6D4'
                ],
                borderColor: '#1e1e1e',
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#a0a0a0'
                    }
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Sales Revenue Trend (Line Chart) - Sales
    const salesCtx = document.getElementById('salesRevenueTrendChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales Revenue',
                data: [5, 3, 4, 10, 5, 7, 6],
                borderColor: '#EAB308', // Yellow
                backgroundColor: 'rgba(234, 179, 8, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#EAB308',
                pointBorderColor: '#EAB308',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Sales Revenue Distribution (Pie Chart) - Sales
    const salesPieCtx = document.getElementById('salesRevenueDistributionChart').getContext('2d');
    new Chart(salesPieCtx, {
        type: 'pie',
        data: {
            labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product D', 'Product D'],
            datasets: [{
                label: 'Sales Revenue Distribution',
                data: [30, 25, 10, 30, 2, 3],
                backgroundColor: [
                    '#0EA5E9',
                    '#F97316',
                    '#A855F7',
                    '#EC4899',
                    '#EF4444',
                    '#06B6D4'
                ],
                borderColor: '#1e1e1e',
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#a0a0a0'
                    }
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Customer Growth Trend (Line Chart) - Customers
    const customerCtx = document.getElementById('customerGrowthTrendChart').getContext('2d');
    new Chart(customerCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Customer Growth',
                data: [50, 55, 60, 58, 62, 65, 70],
                borderColor: '#14B8A6', // Teal
                backgroundColor: 'rgba(20, 184, 166, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#14B8A6',
                pointBorderColor: '#14B8A6',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Operational Efficiency Trend (Line Chart) - Operations
    const operationsCtx = document.getElementById('operationalEfficiencyTrendChart').getContext('2d');
    new Chart(operationsCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Operational Efficiency',
                data: [80, 82, 78, 85, 83, 87, 90],
                borderColor: '#EF4444', // Red
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#EF4444',
                pointBorderColor: '#EF4444',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });

    // Configurazione del grafico Downtime Per Day (Bar Chart) - Operations
    const downtimeCtx = document.getElementById('downtimePerDayChart').getContext('2d');
    new Chart(downtimeCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Downtime (min)',
                data: [10, 15, 8, 20, 12, 5, 18],
                backgroundColor: 'rgba(249, 115, 22, 0.2)',
                borderColor: '#F97316', // Indigo
                borderWidth: 3
            }]
        },
        options: {
            scales: {
                x: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#a0a0a0',
                        stepSize: 5
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });
});
</script>

<script src="assets/script.js"></script>