<form  method="post" action='/' enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" id="file" accept=".xlsx, .xls" />
    <button type="submit" class="btn btn-primary">Загрузить</button>
</form>

<form  method="post" action='/stud' >
    @csrf
    <button type="submit" class="btn btn-primary">Загрузить студентов</button>
</form>