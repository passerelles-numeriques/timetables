export default class Module {
  constructor(name = '', id = 0, startDate = '', endDate = '') {
    this.name = name;
    this.id = id;
    this.startDate = startDate;
    this.endDate = endDate;
  }

  equals(module) {
    return this.id === module.id &&
      this.name === module.name &&
      this.startDate === module.startDate &&
      this.endDate === module.endDate;
  }
}
