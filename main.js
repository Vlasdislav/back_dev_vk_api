jQuery($ => {
    // Показ формы регистрации
    $(document).on("click", "#sign_up", () => {
        let html = `
            <h2>Регистрация</h2>
            <form id="sign_up_form">
                <div class="form-group">
                    <label for="firstname">Имя</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" required value="dasdsa" />
                </div>

                <div class="form-group">
                    <label for="lastname">Фамилия</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" required value="sada" />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required value="daasd@sadsa.com" />
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" name="password" id="password" required value="dsasad" />
                </div>

                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </form>
        `;
        clearResponse();
        $("#content").html(html);
    });

    // Отправка формы
    $(document).on("submit", "#sign_up_form", function(event) {
        // Получение данных формы
        const sign_up_form = $(this);
        const form_data = JSON.stringify(sign_up_form.serializeObject());

        // Отправка данных формы в API
        $.ajax({
            url: "/back_dev_vk_api/authentication-jwt/api/create_user.php",
            type: "POST",
            contentType: "application/json",
            data: form_data,
            success: () => {
                // В случае удачного завершения запроса к серверу,
                // сообщим пользователю, что он успешно зарегистрировался и очистим поля ввода
                $("#response").html("<div class='alert alert-success'>Регистрация завершена успешно. Пожалуйста, войдите</div>");
                sign_up_form.find("input").val("");
            },
            error: (xhr, resp, text) => {
                // При ошибке сообщить пользователю, что регистрация не удалась
                $("#response").html("<div class='alert alert-danger'>Невозможно зарегистрироваться. Пожалуйста, свяжитесь с администратором</div>");
            }
        });
        return false;
    });

    // Показа формы входа
    $(document).on("click", "#login", () => {
        showLoginPage();
    });

    // При отправке формы входа
    $(document).on("submit", "#login_form", function(event) {
        // Получаем данные формы
        const login_form = $(this);
        const form_data = JSON.stringify(login_form.serializeObject());

        // Отправка данных формы в API
        $.ajax({
            url: "/back_dev_vk_api/authentication-jwt/api/login.php",
            type: "POST",
            contentType: "application/json",
            data: form_data,
            success: result => {
                // Сохраним JWT в куки
                setCookie("jwt", result.jwt, 1);
                // Показ домашней страницы и сообщение об успешном входе
                showHomePage();
                $("#response").html("<div class='alert alert-success'>Успешный вход в систему.</div>");

            },
            error: (xhr, resp, text) => {
                // При ошибке сообщим пользователю, что вход в систему не выполнен и очистим поля ввода
                $("#response").html("<div class='alert alert-danger'>Ошибка входа. Email или пароль указан неверно.</div>");
                login_form.find("input").val("");
            }
        });
        return false;
    });

    // Показать домашнюю страницу
    $(document).on("click", "#home", () => {
        showHomePage();
        clearResponse();
    });

    // Показать форму обновления аккаунта
    $(document).on("click", "#update_account", () => {
        showUpdateAccountForm();
    });

    // срабатывание при отправке формы «обновить аккаунт»
    $(document).on("submit", "#update_account_form", function () {

        // Дескриптор для update_account_form
        const update_account_form = $(this);

        // Валидация JWT для проверки доступа
        const jwt = getCookie("jwt");

        // Получаем данные формы
        let update_account_form_obj = update_account_form.serializeObject()

        // Добавим JWT
        update_account_form_obj.jwt = jwt;

        // Преобразуем значения формы в JSON с помощью функции stringify()
        const form_data = JSON.stringify(update_account_form_obj);

        // Отправка данных формы в API
        $.ajax({
            url: "/back_dev_vk_api/authentication-jwt/api/update_user.php",
            type: "POST",
            contentType: "application/json",
            data: form_data,
            success: result => {
                // Сказать, что учетная запись пользователя была обновлена
                $("#response").html("<div class='alert alert-success'>Учетная запись обновлена</div>");
                // Сохраняем новый JWT в cookie
                setCookie("jwt", result.jwt, 1);
            },
            // Показать сообщение об ошибке пользователю
            error: (xhr, resp, text) => {
                if (xhr.responseJSON.message == "Невозможно обновить пользователя") {
                    $("#response").html("<div class='alert alert-danger'>Невозможно обновить пользователя</div>");
                } else if (xhr.responseJSON.message == "Доступ закрыт") {
                    showLoginPage();
                    $("#response").html("<div class='alert alert-success'>Доступ закрыт. Пожалуйста войдите</div>");
                }
            }
        });
        return false;
    });

    // Выйти из системы
    $(document).on("click", "#logout", () => {
        showLoginPage();
        $("#response").html("<div class='alert alert-info'>Вы вышли из системы.</div>");
    });

    // Удаление всех быстрых сообщений
    function clearResponse() {
        $("#response").html("");
    }

    // Функция показывает HTML-форму для входа в систему.
    function showLoginPage() {
        // Удаление jwt
        setCookie("jwt", "", 1);

        // Форма входа
        let html = `
            <h2>Вход</h2>
            <form id="login_form">
                <div class="form-group">
                    <label for="email">Email адрес</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Введите email">
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль">
                </div>
                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        `;

        $("#content").html(html);
        clearResponse();
        showLoggedOutMenu();
    }

    // Функция для показа домашней страницы
    function showHomePage() {

    // Валидация JWT для проверки доступа
        const jwt = getCookie("jwt");

        $.post("/back_dev_vk_api/authentication-jwt/api/validate_token.php", JSON.stringify({ jwt: jwt })).done(result => {

            // если прошел валидацию, показать домашнюю страницу
            let html = `
                <div class="card">
                    <div class="card-header">Добро пожаловать!</div>
                    <div class="card-body">
                        <h5 class="card-title">Вы вошли в систему</h5>
                        <p class="card-text">Вы не сможете получить доступ к домашней странице и страницам учетной записи, если вы не вошли в систему</p>
                    </div>
                </div>
            `;
            $("#content").html(html);
            showLoggedInMenu();
        })
        // Показать страницу входа при ошибке
        .fail(function (result) {
            showLoginPage();
            $("#response").html("<div class='alert alert-danger'>Пожалуйста войдите, чтобы получить доступ к домашней станице</div>");
        });
    }

    // Функция поможет нам прочитать JWT, который мы сохранили ранее.
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(";");
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == " ") {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    // Если пользователь авторизован
    function showLoggedInMenu() {
        // Скроем кнопки входа и регистрации с панели навигации и покажем кнопку выхода
        $("#login, #sign_up").hide();
        $("#logout, #add_announcement").show();
    }

    function showUpdateAccountForm() {
        // Валидация JWT для проверки доступа
        const jwt = getCookie("jwt");
        $.post("/back_dev_vk_api/authentication-jwt/api/validate_token.php", JSON.stringify({ jwt: jwt })).done(result => {

            // Если валидация прошла успешно, покажем данные пользователя в форме
            let html = `
                <h2>Обновление аккаунта</h2>
                <form id="update_account_form">
                    <div class="form-group">
                        <label for="firstname">Имя</label>
                        <input type="text" class="form-control" name="firstname" id="firstname" required value="${result.data.firstname}" />
                    </div>

                    <div class="form-group">
                        <label for="lastname">Фамилия</label>
                        <input type="text" class="form-control" name="lastname" id="lastname" required value="${result.data.lastname}" />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required value="${result.data.email}" />
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" name="password" id="password" />
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Сохранить
                    </button>
                </form>
            `;
            clearResponse();
            $("#content").html(html);
        })
        // В случае ошибки / сбоя сообщите пользователю, что ему необходимо войти в систему, 
        // чтобы увидеть страницу учетной записи
        .fail(result => {
            showLoginPage();
            $("#response").html("<div class='alert alert-danger'>Пожалуйста, войдите, чтобы получить доступ к странице учетной записи</div>");
        });
    }

    // Функция setCookie() поможет нам сохранить JWT в файле cookie
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    // Эта функция сделает меню похожим на опции для пользователя, вышедшего из системы.
    function showLoggedOutMenu() {
        // Показать кнопку входа и регистрации в меню навигации
        $("#login, #sign_up").show();
        $("#logout. #add_announcement").hide();
    }

    // Функция показывает HTML-форму для входа в систему.
    function showLoginPage() {
        // Удаление jwt
        setCookie("jwt", "", 1);
        // Форма входа
        let html = `
            <h2>Вход</h2>
            <form id="login_form">
                <div class="form-group">
                    <label for="email">Email адрес</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Введите email">
                </div>

                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль">
                </div>

                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        `;

        $("#content").html(html);
        clearResponse();
        showLoggedOutMenu();
    }

    // Функция setCookie() поможет нам сохранить JWT в файле cookie
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    // Функция для преобразования значений формы в формат JSON
    $.fn.serializeObject = function () {
        let o = {};
        let a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || "");
            } else {
                o[this.name] = this.value || "";
            }
        });
        return o;
    };

    // // Показать все объявления
    // $(document).on("click", "#announcements", () => {
    //     showAnnouncementsPage();
    // });

    // // Фунция выводит все объявления
    // function showAnnouncementsPage() {

    // }
});
