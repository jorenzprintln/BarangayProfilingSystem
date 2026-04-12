function filterCards() {
    const term      = document.getElementById('searchBar').value.toLowerCase();
    const cards     = document.querySelectorAll('.searchable-card');
    const noResults = document.getElementById('noResults');
    let visible     = 0;

    cards.forEach(card => {
        const label = card.querySelector('.form-card-label').textContent.toLowerCase();
        const show  = label.includes(term);
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    noResults.style.display = visible === 0 ? 'block' : 'none';
}   