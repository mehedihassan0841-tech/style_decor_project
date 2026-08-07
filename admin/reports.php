<?php

session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["user_role"] != "admin"){
    header("Location: ../login.php");
    exit();
}

require_once("../config/database.php");


/* =====================================================
   REPORT FILTER
===================================================== */

$period = $_GET["period"] ?? "6months";


/* =====================================================
   KPI STATISTICS
===================================================== */

/* Total Bookings */

$total_bookings_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM bookings"
);

$total_bookings = mysqli_fetch_assoc($total_bookings_query);


/* Completed Bookings */

$completed_bookings_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM bookings
     WHERE booking_status='Completed'"
);

$completed_bookings = mysqli_fetch_assoc($completed_bookings_query);


/* Accepted Bookings */

$accepted_bookings_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM bookings
     WHERE booking_status='Accepted'"
);

$accepted_bookings = mysqli_fetch_assoc($accepted_bookings_query);


/* Cancelled Bookings */

$cancelled_bookings_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM bookings
     WHERE booking_status='Cancelled'"
);

$cancelled_bookings = mysqli_fetch_assoc($cancelled_bookings_query);


/* Total Revenue */

$total_revenue_query = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount),0) AS total
     FROM bookings
     WHERE booking_status='Completed'"
);

$total_revenue = mysqli_fetch_assoc($total_revenue_query);


/* Total Decorators */

$total_decorators_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role='decorator'
     AND status='active'"
);

$total_decorators = mysqli_fetch_assoc($total_decorators_query);


/* Total Clients */

$total_clients_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role='client'
     AND status='active'"
);

$total_clients = mysqli_fetch_assoc($total_clients_query);


/* Average Rating */

$average_rating_query = mysqli_query(
    $conn,
    "SELECT COALESCE(AVG(rating),0) AS average
     FROM reviews"
);

$average_rating = mysqli_fetch_assoc($average_rating_query);


/* =====================================================
   BOOKING STATUS DATA
===================================================== */

$status_query = mysqli_query(
    $conn,
    "SELECT
        booking_status,
        COUNT(*) AS total
     FROM bookings
     GROUP BY booking_status"
);

$status_data = [];

while($row = mysqli_fetch_assoc($status_query)){

    $status_data[] = $row;

}


/* =====================================================
   CATEGORY BOOKING DATA
===================================================== */

$category_query = mysqli_query(
    $conn,
    "SELECT
        decorator_services.category,
        COUNT(bookings.id) AS total
     FROM bookings
     INNER JOIN decorator_services
        ON bookings.service_id = decorator_services.id
     GROUP BY decorator_services.category
     ORDER BY total DESC"
);

$category_data = [];

while($row = mysqli_fetch_assoc($category_query)){

    $category_data[] = $row;

}


/* =====================================================
   MONTHLY REVENUE
===================================================== */

$monthly_revenue_query = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(created_at,'%Y-%m') AS month,
        SUM(total_amount) AS revenue
     FROM bookings
     WHERE booking_status='Completed'
     GROUP BY DATE_FORMAT(created_at,'%Y-%m')
     ORDER BY month ASC"
);

$monthly_revenue = [];

while($row = mysqli_fetch_assoc($monthly_revenue_query)){

    $monthly_revenue[] = $row;

}


/* =====================================================
   MONTHLY BOOKINGS
===================================================== */

$monthly_booking_query = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(created_at,'%Y-%m') AS month,
        COUNT(*) AS total
     FROM bookings
     GROUP BY DATE_FORMAT(created_at,'%Y-%m')
     ORDER BY month ASC"
);

$monthly_bookings = [];

while($row = mysqli_fetch_assoc($monthly_booking_query)){

    $monthly_bookings[] = $row;

}


/* =====================================================
   TOP DECORATORS
===================================================== */

$top_decorators_query = mysqli_query(
    $conn,
    "SELECT
        users.full_name,
        COUNT(bookings.id) AS total_bookings,
        COALESCE(SUM(
            CASE
                WHEN bookings.booking_status='Completed'
                THEN bookings.total_amount
                ELSE 0
            END
        ),0) AS revenue
     FROM bookings
     INNER JOIN decorator_services
        ON bookings.service_id = decorator_services.id
     INNER JOIN users
        ON decorator_services.decorator_id = users.id
     GROUP BY users.id, users.full_name
     ORDER BY revenue DESC
     LIMIT 5"
);

$top_decorators = [];

while($row = mysqli_fetch_assoc($top_decorators_query)){

    $top_decorators[] = $row;

}


/* =====================================================
   RATING DISTRIBUTION
===================================================== */

$rating_query = mysqli_query(
    $conn,
    "SELECT
        rating,
        COUNT(*) AS total
     FROM reviews
     GROUP BY rating
     ORDER BY rating ASC"
);

$rating_data = [];

while($row = mysqli_fetch_assoc($rating_query)){

    $rating_data[] = $row;

}


/* =====================================================
   PAGE
===================================================== */

include("../includes/admin_header.php");
include("../includes/admin_sidebar.php");

?>
<div class="admin-content">

    <div class="reports-page-wrapper">

        <!-- =========================================
             REPORT HEADER
        ========================================== -->

        <div class="reports-header">

            <div class="reports-header-left">

                <div class="reports-title-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>

                <div>

                    <h1>Reports & Analytics</h1>

                    <p>
                        Track your business performance and insights.
                    </p>

                </div>

            </div>


            <div class="reports-header-right">

                <form method="GET" class="reports-period-form">

                    <i class="fa-regular fa-calendar"></i>

                    <select name="period" onchange="this.form.submit()">

                        <option
                            value="7days"
                            <?php if($period == "7days") echo "selected"; ?>
                        >
                            Last 7 Days
                        </option>

                        <option
                            value="30days"
                            <?php if($period == "30days") echo "selected"; ?>
                        >
                            Last 30 Days
                        </option>

                        <option
                            value="3months"
                            <?php if($period == "3months") echo "selected"; ?>
                        >
                            Last 3 Months
                        </option>

                        <option
                            value="6months"
                            <?php if($period == "6months") echo "selected"; ?>
                        >
                            Last 6 Months
                        </option>

                        <option
                            value="1year"
                            <?php if($period == "1year") echo "selected"; ?>
                        >
                            Last 1 Year
                        </option>

                    </select>

                </form>


                <button
                    type="button"
                    class="reports-refresh-btn"
                    onclick="window.location.reload();"
                    title="Refresh Report"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                </button>


                <button
                    type="button"
                    class="reports-export-btn"
                    onclick="window.print();"
                >
                    <i class="fa-solid fa-download"></i>
                    Export
                </button>

            </div>

        </div>


        <!-- =========================================
             KPI CARDS
        ========================================== -->

        <div class="reports-kpi-grid">


            <!-- REVENUE -->

            <div class="reports-kpi-card revenue-card">

                <div class="reports-kpi-top">

                    <div class="reports-kpi-icon">
                        <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                    </div>

                    <span class="reports-kpi-label">
                        Total Revenue
                    </span>

                </div>

                <div class="reports-kpi-value">

                    ৳<?php
                        echo number_format(
                            $total_revenue["total"]
                        );
                    ?>

                </div>

                <div class="reports-kpi-footer">

                    <span class="reports-kpi-trend positive">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        Revenue
                    </span>

                    <span>
                        Completed bookings
                    </span>

                </div>

            </div>


            <!-- BOOKINGS -->

            <div class="reports-kpi-card booking-card">

                <div class="reports-kpi-top">

                    <div class="reports-kpi-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>

                    <span class="reports-kpi-label">
                        Total Bookings
                    </span>

                </div>

                <div class="reports-kpi-value">

                    <?php
                        echo number_format(
                            $total_bookings["total"]
                        );
                    ?>

                </div>

                <div class="reports-kpi-footer">

                    <span class="reports-kpi-trend positive">
                        <i class="fa-solid fa-arrow-up"></i>
                        Active
                    </span>

                    <span>
                        All bookings
                    </span>

                </div>

            </div>


            <!-- COMPLETED -->

            <div class="reports-kpi-card completed-card">

                <div class="reports-kpi-top">

                    <div class="reports-kpi-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <span class="reports-kpi-label">
                        Completed
                    </span>

                </div>

                <div class="reports-kpi-value">

                    <?php
                        echo number_format(
                            $completed_bookings["total"]
                        );
                    ?>

                </div>

                <div class="reports-kpi-footer">

                    <span class="reports-kpi-trend positive">
                        <i class="fa-solid fa-check"></i>
                        Success
                    </span>

                    <span>
                        Completed events
                    </span>

                </div>

            </div>


            <!-- RATING -->

            <div class="reports-kpi-card rating-card">

                <div class="reports-kpi-top">

                    <div class="reports-kpi-icon">
                        <i class="fa-solid fa-star"></i>
                    </div>

                    <span class="reports-kpi-label">
                        Average Rating
                    </span>

                </div>

                <div class="reports-kpi-value">

                    <?php
                        echo number_format(
                            $average_rating["average"],
                            1
                        );
                    ?>

                    <small>/ 5</small>

                </div>

                <div class="reports-kpi-footer">

                    <span class="reports-kpi-trend positive">
                        <i class="fa-solid fa-star"></i>
                        Customer
                    </span>

                    <span>
                        Average review rating
                    </span>

                </div>

            </div>

        </div>


        <!-- =========================================
             CHART AREA
        ========================================== -->

        <div class="reports-chart-grid">


            <!-- REVENUE OVERVIEW -->

            <div class="reports-chart-card reports-chart-large">

                <div class="reports-chart-header">

                    <div>

                        <h2>Revenue Overview</h2>

                        <p>
                            Completed booking revenue over time.
                        </p>

                    </div>

                    <div class="reports-chart-menu">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>

                </div>

                <div class="reports-chart-container">

                    <div
                        id="revenueChart"
                        class="reports-chart"
                    ></div>

                </div>

            </div>


            <!-- BOOKING STATUS -->

            <div class="reports-chart-card">

                <div class="reports-chart-header">

                    <div>

                        <h2>Booking Status</h2>

                        <p>
                            Current booking distribution.
                        </p>

                    </div>

                    <div class="reports-chart-menu">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>

                </div>

                <div class="reports-chart-container">

                    <div
                        id="bookingStatusChart"
                        class="reports-chart"
                    ></div>

                </div>

            </div>


            <!-- MONTHLY BOOKINGS -->

            <div class="reports-chart-card reports-chart-large">

                <div class="reports-chart-header">

                    <div>

                        <h2>Booking Performance</h2>

                        <p>
                            Booking activity by month.
                        </p>

                    </div>

                    <div class="reports-chart-menu">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>

                </div>

                <div class="reports-chart-container">

                    <div
                        id="bookingChart"
                        class="reports-chart"
                    ></div>

                </div>

            </div>


            <!-- CATEGORY -->

            <div class="reports-chart-card">

                <div class="reports-chart-header">

                    <div>

                        <h2>Category Performance</h2>

                        <p>
                            Bookings by service category.
                        </p>

                    </div>

                    <div class="reports-chart-menu">
                        <i class="fa-solid fa-ellipsis"></i>
                    </div>

                </div>

                <div class="reports-chart-container">

                    <div
                        id="categoryChart"
                        class="reports-chart"
                    ></div>

                </div>

            </div>

        </div>


        <!-- =========================================
             LOWER ANALYTICS
        ========================================== -->

        <div class="reports-bottom-grid">


            <!-- TOP DECORATORS -->

            <div class="reports-data-card">

                <div class="reports-data-header">

                    <div>

                        <h2>Top Decorators</h2>

                        <p>
                            Best performing decorators.
                        </p>

                    </div>

                    <i class="fa-solid fa-trophy"></i>

                </div>


                <div class="reports-decorator-list">

                    <?php if(count($top_decorators) > 0){ ?>

                        <?php
                        $rank = 1;

                        $max_decorator_revenue = max(
                            array_column($top_decorators, "revenue")
                        );
                        ?>

                        <?php foreach($top_decorators as $decorator){ ?>

                            <?php

                            $decorator_revenue = (float)$decorator["revenue"];

                            $decorator_percentage = 0;

                            if($max_decorator_revenue > 0){
                                $decorator_percentage =
                                    ($decorator_revenue / $max_decorator_revenue) * 100;
                            }

                            ?>

                            <div class="reports-decorator-row">

                                <div class="reports-rank">
                                    <?php echo $rank; ?>
                                </div>

                                <div class="reports-decorator-info">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $decorator["full_name"]
                                        );
                                        ?>
                                    </strong>

                                    <span>
                                        <?php
                                        echo $decorator["total_bookings"];
                                        ?>
                                        bookings
                                    </span>

                                    <div class="reports-decorator-performance">

                                        <div class="reports-decorator-performance-track">

                                            <div
                                                class="reports-decorator-performance-fill"
                                                style="width:<?php echo $decorator_percentage; ?>%;"
                                            ></div>

                                        </div>

                                        <span>
                                            <?php echo round($decorator_percentage); ?>%
                                        </span>

                                    </div>

                                </div>

                                <div class="reports-decorator-revenue">

                                    ৳<?php
                                    echo number_format(
                                        $decorator["revenue"]
                                    );
                                    ?>

                                </div>

                            </div>

                            <?php
                            $rank++;
                            ?>

                        <?php } ?>

                    <?php }else{ ?>

                        <div class="reports-empty-state">

                            <i class="fa-solid fa-chart-column"></i>

                            <p>
                                No decorator data available.
                            </p>

                        </div>

                    <?php } ?>

                </div>

            </div>


            <!-- RATING DISTRIBUTION -->

            <div class="reports-data-card">

                <div class="reports-data-header">

                    <div>

                        <h2>Customer Ratings</h2>

                        <p>
                            Review rating distribution.
                        </p>

                    </div>

                    <div class="reports-rating-summary">

                        <strong>
                            <?php
                            echo number_format(
                                $average_rating["average"],
                                1
                            );
                            ?>
                        </strong>

                        <i class="fa-solid fa-star"></i>

                    </div>

                </div>


                <div class="reports-rating-list">

                    <?php

                    $rating_counts = [
                        5 => 0,
                        4 => 0,
                        3 => 0,
                        2 => 0,
                        1 => 0
                    ];

                    foreach($rating_data as $rating){

                        $rating_counts[
                            (int)$rating["rating"]
                        ] = (int)$rating["total"];

                    }

                    $total_reviews =
                        array_sum($rating_counts);

                    ?>


                    <?php for($star = 5; $star >= 1; $star--){ ?>

                        <?php

                        $percentage = 0;

                        if($total_reviews > 0){

                            $percentage =
                                ($rating_counts[$star] /
                                $total_reviews) * 100;

                        }

                        ?>

                        <div class="reports-rating-row">

                            <span>
                                <?php echo $star; ?>
                                <i class="fa-solid fa-star"></i>
                            </span>

                            <div class="reports-rating-bar">

                                <div
                                    style="width:<?php echo $percentage; ?>%;"
                                ></div>

                            </div>

                            <strong>
                                <?php
                                echo $rating_counts[$star];
                                ?>
                            </strong>

                        </div>

                    <?php } ?>

                </div>

            </div>

        </div>


        <!-- =========================================
             QUICK INSIGHTS
        ========================================== -->

        <div class="reports-insights-card">

            <div class="reports-insights-icon">

                <i class="fa-solid fa-lightbulb"></i>

            </div>

            <div>

                <h3>Business Insights</h3>

                <p>
                    Use your booking, revenue and customer
                    data to understand which services and
                    decorators are performing best.
                </p>

            </div>

        </div>


    </div>

</div>
<script>

document.addEventListener("DOMContentLoaded", function(){

    /* =================================================
       MONTHLY REVENUE DATA
    ================================================= */

    const revenueData = <?php echo json_encode($monthly_revenue); ?>;

    const revenueMonths = revenueData.map(function(item){
        return item.month;
    });

    const revenueValues = revenueData.map(function(item){
        return Number(item.revenue);
    });


    /* =================================================
       REVENUE CHART
    ================================================= */

    const revenueElement = document.querySelector("#revenueChart");

    if(revenueElement){

        const revenueOptions = {

            series: [{
                name: "Revenue",
                data: revenueValues
            }],

            chart: {
                type: "area",
                height: 300,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                fontFamily: "Poppins, sans-serif"
            },

            colors: ["#16a34a"],

            stroke: {
                curve: "smooth",
                width: 3
            },

            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.28,
                    opacityTo: 0.03,
                    stops: [0, 90, 100]
                }
            },

            dataLabels: {
                enabled: false
            },

            markers: {
                size: 0,
                hover: {
                    size: 6
                }
            },

            xaxis: {
                categories: revenueMonths,
                labels: {
                    style: {
                        colors: "#94a3b8",
                        fontSize: "11px"
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },

            yaxis: {
                labels: {
                    formatter: function(value){
                        return "৳" + Number(value).toLocaleString();
                    },
                    style: {
                        colors: "#94a3b8",
                        fontSize: "11px"
                    }
                }
            },

            grid: {
                borderColor: "#f1f5f9",
                strokeDashArray: 4
            },

            tooltip: {
                theme: "light",
                y: {
                    formatter: function(value){
                        return "৳" + Number(value).toLocaleString();
                    }
                }
            }
        };

        const revenueChart =
            new ApexCharts(
                revenueElement,
                revenueOptions
            );

        revenueChart.render();
    }


    /* =================================================
       MONTHLY BOOKING DATA
    ================================================= */

    const bookingData = <?php echo json_encode($monthly_bookings); ?>;

    const bookingMonths = bookingData.map(function(item){
        return item.month;
    });

    const bookingValues = bookingData.map(function(item){
        return Number(item.total);
    });


    /* =================================================
       BOOKING PERFORMANCE CHART
    ================================================= */

    const bookingElement =
        document.querySelector("#bookingChart");

    if(bookingElement){

        const bookingOptions = {

            series: [{
                name: "Bookings",
                data: bookingValues
            }],

            chart: {
                type: "bar",
                height: 300,
                toolbar: {
                    show: false
                },
                fontFamily: "Poppins, sans-serif"
            },

            colors: ["#22c55e"],

            plotOptions: {

                bar: {
                    borderRadius: 7,
                    columnWidth: "48%"
                }

            },

            dataLabels: {
                enabled: false
            },

            xaxis: {

                categories: bookingMonths,

                labels: {
                    style: {
                        colors: "#94a3b8",
                        fontSize: "11px"
                    }
                },

                axisBorder: {
                    show: false
                },

                axisTicks: {
                    show: false
                }

            },

            yaxis: {

                labels: {
                    style: {
                        colors: "#94a3b8",
                        fontSize: "11px"
                    }
                }

            },

            grid: {
                borderColor: "#f1f5f9",
                strokeDashArray: 4
            },

            tooltip: {
                theme: "light",
                y: {
                    formatter: function(value){
                        return value + " bookings";
                    }
                }
            }

        };

        const bookingChart =
            new ApexCharts(
                bookingElement,
                bookingOptions
            );

        bookingChart.render();

    }


    /* =================================================
       BOOKING STATUS DATA
    ================================================= */

    const statusData = <?php echo json_encode($status_data); ?>;

    const statusLabels = statusData.map(function(item){
        return item.booking_status;
    });

    const statusValues = statusData.map(function(item){
        return Number(item.total);
    });


    /* =================================================
       BOOKING STATUS CHART
    ================================================= */

    const statusElement =
        document.querySelector("#bookingStatusChart");

    if(statusElement){

        const statusOptions = {

            series: statusValues,

            labels: statusLabels,

            chart: {
                type: "donut",
                height: 290,
                fontFamily: "Poppins, sans-serif"
            },

            colors: [
                "#f59e0b",
                "#22c55e",
                "#3b82f6",
                "#ef4444"
            ],

            stroke: {
                width: 4,
                colors: ["#ffffff"]
            },

            legend: {
                position: "bottom",
                fontSize: "12px",
                labels: {
                    colors: "#64748b"
                },
                markers: {
                    width: 8,
                    height: 8,
                    radius: 10
                }
            },

            dataLabels: {
                enabled: false
            },

            plotOptions: {

                pie: {

                    donut: {

                        size: "70%",

                        labels: {

                            show: true,

                            name: {
                                show: true,
                                color: "#64748b",
                                fontSize: "12px"
                            },

                            value: {
                                show: true,
                                color: "#0f172a",
                                fontSize: "24px",
                                fontWeight: 700
                            },

                            total: {
                                show: true,
                                label: "Bookings",
                                color: "#64748b",
                                formatter: function(){
                                    return statusValues.reduce(
                                        function(total, value){
                                            return total + value;
                                        },
                                        0
                                    );
                                }
                            }

                        }

                    }

                }

            },

            tooltip: {
                y: {
                    formatter: function(value){
                        return value + " bookings";
                    }
                }
            }

        };

        const statusChart =
            new ApexCharts(
                statusElement,
                statusOptions
            );

        statusChart.render();

    }


    /* =================================================
       CATEGORY DATA
    ================================================= */

    const categoryData =
        <?php echo json_encode($category_data); ?>;

    const categoryLabels =
        categoryData.map(function(item){
            return item.category;
        });

    const categoryValues =
        categoryData.map(function(item){
            return Number(item.total);
        });


    /* =================================================
       CATEGORY CHART
    ================================================= */

    const categoryElement =
        document.querySelector("#categoryChart");

    if(categoryElement){

        const categoryOptions = {

            series: [{

                name: "Bookings",

                data: categoryValues

            }],

            chart: {

                type: "bar",

                height: 290,

                toolbar: {
                    show: false
                },

                fontFamily: "Poppins, sans-serif"

            },

            colors: ["#16a34a"],

            plotOptions: {

                bar: {

                    horizontal: true,

                    borderRadius: 7,

                    barHeight: "55%"

                }

            },

            dataLabels: {

                enabled: false

            },

            xaxis: {

                categories: categoryLabels,

                labels: {

                    style: {

                        colors: "#94a3b8",

                        fontSize: "11px"

                    }

                },

                axisBorder: {
                    show: false
                },

                axisTicks: {
                    show: false
                }

            },

            yaxis: {

                labels: {

                    style: {

                        colors: "#475569",

                        fontSize: "11px",

                        fontWeight: 600

                    }

                }

            },

            grid: {

                borderColor: "#f1f5f9",

                strokeDashArray: 4

            },

            tooltip: {

                theme: "light",

                y: {

                    formatter: function(value){

                        return value + " bookings";

                    }

                }

            }

        };

        const categoryChart =
            new ApexCharts(
                categoryElement,
                categoryOptions
            );

        categoryChart.render();

    }

});

</script>

<?php include("../includes/admin_footer.php"); ?>