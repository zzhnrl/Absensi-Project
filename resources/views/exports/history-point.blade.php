<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Perubahan Poin</th>
            <th>Total Poin</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $row->perubahan_point }}</td>
            <td>{{ $row->jumlah_point }}</td>
            <td>{{ $row->tanggal->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
