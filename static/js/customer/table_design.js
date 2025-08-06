 const template = document.getElementById('table-template');
    const cells = document.querySelectorAll('.grid > div');

    cells.forEach((cell) => {
      const node = template.content.cloneNode(true);
      const name = cell.textContent.trim();
      const orders = name === 'C2' || name === 'A2' || name === 'B1' ? 1 : 0;

      node.querySelector('.table-name').textContent = name;
      // node.querySelector('.table-orders').textContent = `Orders: ${orders}`;
      node.querySelector('.table-orders').textContent = ``;
      
      if (orders > 0) {
        node.querySelector('div').classList.add('bg-amber-100', 'border-amber-500');
        node.querySelector('.table-name').classList.add('text-orange-800');
        node.querySelector('.table-orders').classList.add('text-orange-500', 'font-semibold');
      }

      cell.textContent = '';
      cell.appendChild(node);
    });