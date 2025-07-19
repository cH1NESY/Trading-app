<!DOCTYPE html>
<html lang="ru">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
            </style>
    </head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Trading App</h1>
                    <p class="lead mb-4">Полнофункциональная система управления торговыми заказами с автоматическим учетом остатков товаров на складах.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-tachometer-alt"></i> Перейти к системе
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle"></i> Узнать больше
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-shopping-cart" style="font-size: 8rem; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5" id="features">
        <h2 class="text-center mb-5">Возможности системы</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-warehouse text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Управление складами</h5>
                        <p class="card-text">Просмотр списка складов и управление их информацией.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-box text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Учет товаров</h5>
                        <p class="card-text">Отслеживание остатков товаров по всем складам в реальном времени.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Управление заказами</h5>
                        <p class="card-text">Создание, редактирование и управление статусами заказов.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">API Endpoints</h2>
            <div class="row">
                <div class="col-md-6">
                    <h5>Склады</h5>
                    <ul class="list-unstyled">
                        <li><code>GET /api/warehouses</code> - Список складов</li>
                    </ul>
                    
                    <h5>Товары</h5>
                    <ul class="list-unstyled">
                        <li><code>GET /api/products</code> - Список товаров с остатками</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Заказы</h5>
                    <ul class="list-unstyled">
                        <li><code>GET /api/orders</code> - Список заказов</li>
                        <li><code>POST /api/orders</code> - Создать заказ</li>
                        <li><code>GET /api/orders/{id}</code> - Просмотр заказа</li>
                        <li><code>PUT /api/orders/{id}</code> - Обновить заказ</li>
                        <li><code>PATCH /api/orders/{id}/complete</code> - Завершить заказ</li>
                        <li><code>PATCH /api/orders/{id}/cancel</code> - Отменить заказ</li>
                        <li><code>PATCH /api/orders/{id}/resume</code> - Возобновить заказ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Trading App. Все права защищены.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
