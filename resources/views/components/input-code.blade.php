<div style="display: flex;">
    <form action="/sendCode" method="post">
        @csrf
        <label for="code">input ur code from Telegram</label>
        <input id="code" name="code" type="text" placeholder="*******" pattern="[0-9]{4,6}">
        <input type="submit">
    </form>
</div>
