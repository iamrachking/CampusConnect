
    function confirmDelete(classroomId) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Cette action est irréversible et supprimera cet élément.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            willOpen: () => {
                // la taille de l'icone
                const icon = Swal.getIcon();
                icon.style.width = '50px'; 
                icon.style.height = '50px'; 
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + classroomId).submit();
            }
        });
    }

