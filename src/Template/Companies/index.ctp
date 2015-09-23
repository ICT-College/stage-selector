<table>
    <thead>

    </thead>
    <tbody>
    <?php foreach ($companies as $company): ?>
        <tr>
            <td><?= h($company->name); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo $this->Paginator->numbers(['first' => 2, 'last' => 2]); ?>
