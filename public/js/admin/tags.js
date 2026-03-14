$(document).ready(function () {
  let table = $('#tags-table').DataTable({
    order: [[0, 'desc']],
    serverSide: true,
    ajax: {
      url: window.location.href,
    },
    columns: [
      { data: 'id', name: 'id' },
      { data: 'name', name: 'name' },
      { data: 'posts_count', name: 'posts_count' },
      { data: 'created_at', name: 'created_at', searchable: false },
      { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
  });

  $(document).on('click', '#tags-table .delete-resource', function (e) {
    e.preventDefault();
    deleteResource(table, $(this).data('link'));
  });
});
