<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_services_db";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT id, title, image, description, date_posted FROM announcements ORDER BY date_posted DESC LIMIT 12";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/student_theme.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #232323;
            font-family: 'Montserrat', 'Roboto', Arial, sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }
        .top-gap {
            width: 100%;
            height: 30px; /* Small gap between header and carousel */
            background: #fff;
        }
        .slideshow-container {
            max-width: 1080px;
            width: 100vw;
            margin: 0 auto 0 auto;
            background: #fff;
            position: relative;
            height: 490px;
            box-shadow: 0 14px 52px rgba(0,51,102,0.20), 0 2px 0 #FFD70044;
            border-radius: 0;
            overflow: visible;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .carousel-slider-main {
            width: 100%;
        }
        .carousel-slide {
            position: relative;
            height: 490px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            background: transparent;
        }
        .carousel-img {
            width: 100%;
            height: 435px;
            object-fit: contain; /* Show full image, no crop */
            border-radius: 0;
            background: #222;
            display: block;
            cursor: pointer;
            transition: box-shadow 0.25s;
            box-shadow: 0 0 12px #0003;
        }
        .carousel-img:hover {
            box-shadow: 0 0 38px #FFD70066;
        }
        .carousel-info-bottom {
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            z-index: 4;
            background: rgba(34,34,34,0.87);
            padding: 7px 14px 4px 14px;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 -2px 14px #0008;
            min-width: 140px;
            max-width: 65vw;
            width: auto;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .carousel-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 0.97rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 1px;
            letter-spacing: 1px;
            text-shadow: 0 1px 8px #000a;
            line-height: 1.1;
        }
        .carousel-date {
            font-size: .82rem;
            color: #FFD700;
            font-weight: 500;
            margin-bottom: 0;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 6px #00336644;
        }
        .carousel-desc {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 1.08rem;
            font-weight: 500;
            color: #232323;
            margin-top: 9px;
            margin-bottom: 6px;
            line-height: 1.48;
            text-align: center;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        .carousel-thumbs-wrap {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
        }
        .carousel-thumb {
            width: 48px;
            height: 36px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 1px 6px #0005;
            cursor: pointer;
            border: 2px solid transparent;
            background: #232323;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .carousel-thumb.active,
        .carousel-thumb:hover {
            border: 2px solid #FFD700;
            box-shadow: 0 2px 12px #FFD70033;
        }
        /* Slick Custom Arrows */
        .slick-prev, .slick-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #FFD700ee !important;
            color: #333 !important;
            border: none;
            font-size: 2.2rem;
            padding: 7px 13px;
            z-index: 44;
            border-radius: 50%;
            box-shadow: 0 2px 15px #00336644;
            transition: background 0.25s, color 0.25s, transform 0.25s;
            cursor: pointer;
            opacity: 0.96;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        .slick-prev { left: -36px; }
        .slick-next { right: -36px; }
        .slick-prev:hover, .slick-next:hover {
            background: #232323 !important;
            color: #FFD700 !important;
            opacity: 1;
            transform: scale(1.14);
        }
        .slick-arrow.slick-hidden {
            display: none !important;
        }
        .slick-slider .slick-arrow.slick-prev:before,
        .slick-slider .slick-arrow.slick-next:before {
            display: none !important;
            content: "" !important;
        }
        .slick-dots {
            bottom: -30px;
            text-align: center;
        }
        .slick-dots li button:before {
            color: #FFD700;
            font-size: 22px;
        }
        .slick-dots li.slick-active button:before {
            color: #232323;
        }
        /* Preview Modal */
        .preview-modal-bg {
            display: none;
            position: fixed;
            left: 0; top: 0;
            width: 100vw; height: 100vh;
            background: rgba(34,34,34,0.82);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .preview-modal-bg.active {
            display: flex;
        }
        .preview-modal {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 44px #00336666;
            max-width: 900px;
            width: 98vw;
            padding: 38px 28px 28px 28px;
            position: relative;
            text-align: left;
            animation: popIn 0.25s cubic-bezier(.4,2,.3,1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        @keyframes popIn {0%{transform:scale(.85);}100%{transform:scale(1);}}
        .preview-modal-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 1.45rem;
            color: #003366;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }
        .preview-modal-date {
            font-size: .99rem;
            color: #FFD700;
            font-weight: 600;
            margin-bottom: 13px;
            text-align: center;
        }
        .preview-modal-img {
            width: 100%;
            height: 480px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 18px;
            background: #232323;
            box-shadow: 0 2px 10px #00336622;
        }
        .preview-modal-desc {
            font-family: 'Roboto', Arial, sans-serif;
            font-size: 1.15rem;
            color: #232323;
            margin-bottom: 0;
            line-height: 1.55;
            text-align: center;
            max-width: 650px;
        }
        .preview-modal-close {
            position: absolute;
            right: 24px;
            top: 18px;
            font-size: 2.1rem;
            color: #003366;
            background: transparent;
            border: none;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        .preview-modal-close:hover {
            opacity: 1;
        }
        footer.site-footer {
            background: #003366;
            color: #FFD700;
            padding: 18px 0 14px 0;
            text-align: center;
            font-size: 1rem;
            font-family: 'Montserrat', Arial, sans-serif;
            border-top: 3px solid #FFD700;
            letter-spacing: 0.5px;
            box-shadow: 0px -2px 7px rgba(0,51,102,0.08);
            margin-top: 0;
            width: 100%;
            flex-shrink: 0;
        }
        @media (max-width:1200px) {
            .slideshow-container {max-width: 100vw;}
            .carousel-img {width: 100vw;}
            .carousel-info-bottom {max-width: 98vw;}
            .preview-modal {max-width: 98vw;}
            .preview-modal-img {height: 260px;}
        }
        @media (max-width:900px) {
            .preview-modal-img {height: 200px;}
            .preview-modal {padding: 18px 7px;}
        }
        @media (max-width:570px) {
            .carousel-title {font-size: .88rem;}
            .carousel-info-bottom {padding: 7px 2vw;}
            .carousel-desc {font-size: .96rem;}
            .preview-modal-title {font-size: 1.07rem;}
            .preview-modal-img {height: 110px;}
        }
    </style>
</head>
<body>
    <?php include('student_header.php'); ?>
    
    <main>
        <div class="top-gap"></div>
        <div class="slideshow-container">
            <div class="carousel-slider-main">
                <?php if ($result && $result->num_rows > 0):
                    $announcements = [];
                    while ($row = $result->fetch_assoc()) $announcements[] = $row;
                    foreach ($announcements as $i => $row): ?>
                    <div class="carousel-slide" data-index="<?= $i ?>">
                        <img class="carousel-img"
                            src="uploads/announcements/<?= htmlspecialchars($row['image']) ?>"
                            alt="<?= htmlspecialchars($row['title']) ?>"
                            data-title="<?= htmlspecialchars($row['title']) ?>"
                            data-date="<?= date('M j, Y', strtotime($row['date_posted'])) ?>"
                            data-desc="<?= htmlspecialchars($row['description']) ?>"
                            data-img="uploads/announcements/<?= htmlspecialchars($row['image']) ?>">
                        <div class="carousel-info-bottom">
                            <div class="carousel-title"><?= htmlspecialchars($row['title']) ?></div>
                            <div class="carousel-date">
                                <i class="fa fa-calendar-alt"></i>
                                <?= date('M j, Y', strtotime($row['date_posted'])) ?>
                            </div>
                        </div>
                        <div class="carousel-desc">
                            <?= htmlspecialchars(mb_strimwidth($row['description'], 0, 120, '...')) ?>
                        </div>
                    </div>
                    <?php endforeach;
                else: ?>
                    <div style="padding:80px 0;text-align:center;color:#FFD700;font-size:1.15rem;">
                        <i class="fa fa-info-circle"></i> No announcements available at the moment.
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($announcements)): ?>
            <div class="carousel-thumbs-wrap">
                <?php foreach ($announcements as $i => $row): ?>
                    <img class="carousel-thumb" data-index="<?= $i ?>"
                        src="uploads/announcements/<?= htmlspecialchars($row['image']) ?>"
                        alt="<?= htmlspecialchars($row['title']) ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <div class="preview-modal-bg" id="previewModalBg">
        <div class="preview-modal" id="previewModal">
            <button class="preview-modal-close" id="previewModalClose" title="Close">&times;</button>
            <div class="preview-modal-title" id="previewModalTitle"></div>
            <div class="preview-modal-date" id="previewModalDate"></div>
            <img class="preview-modal-img" id="previewModalImg" src="" alt="">
            <div class="preview-modal-desc" id="previewModalDesc"></div>
        </div>
    </div>
    <?php include('student_footer.php'); ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
    $(document).ready(function(){
        var $mainSlider = $('.carousel-slider-main');
        $mainSlider.slick({
            dots: false,
            infinite: true,
            speed: 700,
            fade: false,
            autoplay: true,
            autoplaySpeed: 3700,
            arrows: true,
            pauseOnHover: true,
            prevArrow: '<button class="slick-prev" aria-label="Previous"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button class="slick-next" aria-label="Next"><i class="fa fa-chevron-right"></i></button>',
            adaptiveHeight: false,
            cssEase: 'cubic-bezier(.4,2,.3,1)'
        });

        // Thumbnail navigation
        $('.carousel-thumb').on('click', function(){
            var idx = $(this).data('index');
            $mainSlider.slick('slickGoTo', idx);
        });
        $mainSlider.on('afterChange', function(event, slick, currentSlide){
            $('.carousel-thumb').removeClass('active');
            $('.carousel-thumb[data-index="'+currentSlide+'"]').addClass('active');
        });
        $('.carousel-thumb[data-index="0"]').addClass('active');

        // Image Preview Modal
        $('.carousel-img').on('click', function(){
            var title = $(this).data('title');
            var date = $(this).data('date');
            var desc = $(this).data('desc');
            var img = $(this).data('img');
            $('#previewModalTitle').text(title);
            $('#previewModalDate').html('<i class="fa fa-calendar-alt"></i> ' + date);
            $('#previewModalImg').attr('src', img).attr('alt', title);
            $('#previewModalDesc').text(desc);
            $('#previewModalBg').addClass('active');
            $('body').css('overflow','hidden');
        });
        $('#previewModalBg, #previewModalClose').on('click', function(e){
            if(e.target === this) {
                $('#previewModalBg').removeClass('active');
                $('body').css('overflow','');
            }
        });
        $('#previewModal').on('click', function(e){
            e.stopPropagation();
        });
    });
    </script>
</body>
</html>