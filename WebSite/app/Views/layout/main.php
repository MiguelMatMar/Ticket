<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>DonDigital - <?php echo $title?></title>
    <meta name="description" content="Zona Clientes DonDigital">
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/leftbar.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link rel="stylesheet" href="/css/index_cliente.css">
    <link rel="stylesheet" href="/css/faq.css">
    <link rel="stylesheet" href="/css/form_ticket.css">
    <link rel="stylesheet" href="/css/ticket.css">
    <link rel="stylesheet" href="/css/option_tickets.css">
    <link rel="stylesheet" href="/css/generals.css">
    <link rel="stylesheet" href="/css/accDetails.css">
    <link rel="stylesheet" href="/css/ticket_list.css">
    
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Lora:ital,wght@0,400..700;1,400..700&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Oswald:wght@200..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/2c7dfaf499.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="main-wrapper">
        <?php include 'leftbar.php'; ?>
        <?php echo $content; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="/js/nav.js"></script>
    <script src="/js/leftbar.js"></script>
    <script src="/js/ticket_list.js"></script> <script src="/js/ticket.js"></script>
    <script src="/js/open_ticket.js"></script>
    <script src="/js/user_dashboard.js"></script>
    <script src="/js/updatePassword.js"></script>
</body>
</html>