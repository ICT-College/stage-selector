<table class="table table-hover">
    <thead>
    <tr>
        <td>Position id</td>
        <td>Student id</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($placementStudents as $placementStudent): ?>
        <tr>
            <td><?= $placementStudent->position_id; ?></td>
            <td><?= $placementStudent->student_id; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

