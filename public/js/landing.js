document.addEventListener('DOMContentLoaded', () => {
    const peopleInput = document.getElementById('people-count');
    const ingredientsList = document.getElementById('ingredients');

    // Base ingredients for 1 person (Paneer Tikka)
    const baseRecipe = [
        { name: 'Paneer', amount: 100, unit: 'g' },
        { name: 'Onion', amount: 0.5, unit: 'pcs' },
        { name: 'Tomato', amount: 1, unit: 'pcs' },
        { name: 'Curd', amount: 2, unit: 'tbsp' },
        { name: 'Oil', amount: 1, unit: 'tbsp' }
    ];

    function updateIngredients() {
        let people = parseInt(peopleInput.value);
        if (isNaN(people) || people < 1) {
            people = 1;
        }
        if (people > 100) {
            people = 100;
        }
        
        ingredientsList.innerHTML = '';
        
        baseRecipe.forEach(item => {
            const li = document.createElement('li');
            const totalAmount = (item.amount * people).toFixed(item.amount % 1 !== 0 ? 1 : 0);
            
            li.innerHTML = `
                <span>${item.name}</span>
                <strong>${totalAmount} ${item.unit}</strong>
            `;
            ingredientsList.appendChild(li);
        });
    }

    // Initial render
    updateIngredients();

    // Add event listener
    peopleInput.addEventListener('input', updateIngredients);
});
