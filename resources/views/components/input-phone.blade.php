<div style="display: flex;">
    <form action="/sendPhone" method="post">
        @csrf
        <label for="phone">input ur phone</label>
        <input id="phone" type="tel" placeholder="7 (___) ___-__-__" name="phone">
        <input type="submit">
    </form>
</div>
