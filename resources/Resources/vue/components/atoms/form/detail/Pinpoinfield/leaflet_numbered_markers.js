//https://gist.github.com/comp615/2288108
import L from 'leaflet'

L.NumberedDivIcon = L.Icon.extend({
  options: {
    iconUrl: '/img/icon/marker.webp',
    number: '',
    shadowUrl: null,
    iconSize: new L.Point(46, 60),
    iconAnchor: new L.Point(23, 65),
    popupAnchor: new L.Point(0, -62),
    /*
        iconAnchor: (Point)
        popupAnchor: (Point)
        */
    className: 'leaflet-div-icon',
  },

  createIcon: function () {
    const div = document.createElement('div')
    const img = this._createImg(this.options['iconUrl'])
    const numdiv = document.createElement('div')
    numdiv.setAttribute('class', 'number')
    numdiv.innerHTML = this.options['number'] || ''
    div.appendChild(img)
    div.appendChild(numdiv)
    this._setIconStyles(div, 'icon')
    return div
  },

  //you could change this to add a shadow like in the normal marker if you really wanted
  createShadow: function () {
    return null
  },
})
