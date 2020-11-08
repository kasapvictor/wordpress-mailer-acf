<div class="w-form">
    <form id="email-form" name="email-form" data-name="Email Form">

        <!-- name -->
        <input
            type="text"
            class="w-input"
            maxlength="256"
            data-name="Имя"
            placeholder="Name"
            id="name"
            required="">

        <!-- email -->
        <input
            type="email"
            class="w-input"
            maxlength="256"
            data-name="Email"
            placeholder="ivan@example.com"
            id="email"
            required="">

        <!-- message -->
        <textarea
            placeholder="Some message"
            maxlength="5000"
            id="field"
            data-name="Сообщение"
            class="textarea w-input"></textarea>

        <!-- groups -->
        <div data-form-group="Feel">
            <label class="w-checkbox">
                <input type="checkbox" id="Bad" data-name="Bad" class="w-checkbox-input">
                <span for="Bad" class="w-form-label">Bad</span>
            </label>
            <label class="w-checkbox">
                <input type="checkbox" id="Good" data-name="Good" class="w-checkbox-input">
                <span for="Good" class="w-form-label">Good</span>
            </label>
            <label class="w-checkbox">
                <input type="checkbox" id="Great" data-name="Great" class="w-checkbox-input">
                <span for="Great" class="w-form-label">Great</span>
            </label>
        </div>

        <!-- file -->
        <div for="file" class="file"></div>

        <!-- agree -->
        <label class="w-checkbox checkbox-field">
            <input
                type="checkbox"
                id="checkbox"
                data-name="Политика"
                class="w-checkbox-input checkbox"
                required="">
            <span class="checkbox-label w-form-label">Agree ...</span>
        </label>
        <br>

        <!-- recaptcha -->
        <div class="w-form-formrecaptcha recaptcha g-recaptcha g-recaptcha-error g-recaptcha-disabled g-recaptcha-invalid-key"></div>
        <br>

        <!-- submit -->
        <div data-w-id="27bccd5c-4a3d-d021-8f59-edbc5639775b" class="wrap-submit">
            <input
                type="submit"
                value="SEND"
                data-wait="Please wait..."
                class="submit-button w-button">
            <div style="height:50%;background-color:rgb(255,99,71)" class="submit-bg"></div>
        </div>
    </form>
    <!-- done -->
    <div class="w-form-done">
        <div>Thank you! Your submission has been received!</div>
    </div>

    <!-- fail -->
    <div class="w-form-fail">
        <div>Oops! Something went wrong while submitting the form.</div>
    </div>
</div>