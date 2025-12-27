<?php
// pada head.php ini, jika $pageTitle tidak didefinisikan nilai nya, maka gunakan default "Dashboard" sebagai judul halaman
if (!isset($pageTitle)) {
    $pageTitle = "Dashboard";
}
?>
<!DOCTYPE html>
<html lang="end">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Nano Komputer Admin</title>
    <link rel="icon" href="../../assets/img/logo-nano-transparant.png" type="image/x-icon">
    <link rel="stylesheet" href="../../src/output.css">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Sidebar JS -->
    <script src="../../assets/js/admin/sidebar.js" defer></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- jsPDF for PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .icon-button {
            position: relative;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-button:hover {
            background-color: #f1f3f5;
            color: #2563eb;
        }

        .icon-button .material-symbols-outlined {
            font-size: 1.5rem;
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 9999px;
            min-width: 16px;
            text-align: center;
            line-height: 1;
        }

        .profile-photo {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
            transition: border-color 0.2s ease;
        }

        .icon-button:hover .profile-photo {
            border-color: #2563eb;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            min-width: 220px;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            z-index: 50;
            overflow: hidden;
            animation: dropdownFadeIn 0.2s ease;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu.hidden {
            display: none;
        }

        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fafafa;
        }

        .dropdown-header-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1f2937;
        }

        .dropdown-header-email {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-badge.super_admin {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .role-badge.admin {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .role-badge.moderator {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            transition: background-color 0.15s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .dropdown-item .material-symbols-outlined {
            font-size: 1.25rem;
            color: #6b7280;
        }

        .dropdown-item:hover .material-symbols-outlined {
            color: #2563eb;
        }

        .dropdown-item.logout {
            color: #dc2626;
            border-top: 1px solid #e5e7eb;
        }

        .dropdown-item.logout .material-symbols-outlined {
            color: #dc2626;
        }

        .dropdown-item.logout:hover {
            background-color: #fef2f2;
        }

        @media (max-width: 768px) {
            .navbar-right {
                gap: 0.5rem;
            }

            .dropdown-menu {
                min-width: 200px;
            }
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
            text-align: center;
        }

        .modal-icon {
            font-size: 3rem;
            color: #dc2626;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .modal-icon .material-symbols-outlined {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .modal-description {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
        }

        .modal-btn {
            flex: 1;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .modal-btn-cancel {
            background: #f1f3f5;
            color: #212529;
        }

        .modal-btn-cancel:hover {
            background: #e9ecef;
        }

        .modal-btn-logout {
            background: #dc2626;
            color: white;
        }

        .modal-btn-logout:hover {
            background: #b91c1c;
        }

        .modal-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>