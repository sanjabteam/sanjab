<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            @foreach($data['content'] as $row)
                <tr>
                    @foreach($row as $col)
                        <td>
                            {{ $col }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
